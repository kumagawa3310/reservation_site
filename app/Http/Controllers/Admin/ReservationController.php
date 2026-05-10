<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Reservation;
use App\Models\ReservationSlot;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use App\Mail\ReservationCancelled;
use Carbon\Carbon;

class ReservationController extends Controller
{
    /**
     * 1. & 4. 予約一覧ページ（検索機能付き）
     */
    public function index(Request $request)
    {
        $query = Reservation::with(['stayPlan.room'])->orderBy('check_in_date', 'desc');

        // 検索: 名前またはメール
        if ($request->filled('keyword')) {
            $keyword = $request->keyword;
            $query->where(function($q) use ($keyword) {
                $q->where('guest_name', 'like', "%{$keyword}%")
                  ->orWhere('guest_email', 'like', "%{$keyword}%");
            });
        }

        // 検索: ステータス
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // 日付ショートカット検索
    if ($request->filled('date_range')) {
        $today = Carbon::today();
        
        switch ($request->date_range) {
            case 'today':
                $query->whereDate('check_in_date', $today);
                break;
            case 'tomorrow':
                $query->whereDate('check_in_date', $today->addDay());
                break;
            case 'week':
                $query->whereBetween('check_in_date', [$today, $today->copy()->addDays(7)]);
                break;
        }
    }

        $reservations = $query->paginate(20);

        return view('admin.reservations.index', compact('reservations'));
    }

    /**
     * 2. 予約詳細ページ
     */
    public function show(Reservation $reservation)
    {
        $reservation->load(['stayPlan.room']);
        return view('admin.reservations.show', compact('reservation'));
    }

    /**
     * 3. メモ機能の保存
     */
    public function updateMemo(Request $request, Reservation $reservation)
    {
        $request->validate(['admin_memo' => 'nullable|string|max:1000']);
        
        $reservation->update(['admin_memo' => $request->admin_memo]);

        return back()->with('success', 'メモを保存しました。');
    }

    /**
     * 5. & 6. 予約キャンセル（枠の解放・メール送信）
     */
    public function cancel(Reservation $reservation)
    {
        if ($reservation->status === Reservation::STATUS_CANCELLED) {
            return back()->with('error', '既にキャンセルされています。');
        }

        try {
            DB::transaction(function () use ($reservation) {
                $reservation->update(['status' => Reservation::STATUS_CANCELLED]);

                $checkIn  = $reservation->check_in_date->copy();
                $checkOut = $reservation->check_out_date->copy();

                // 各日付につき reserved なスロットを1つだけ available に戻す
                while ($checkIn->lt($checkOut)) {
                    $slot = ReservationSlot::where('room_id', $reservation->room_id)
                        ->where('date', $checkIn->toDateString())
                        ->where('status', 'reserved')
                        ->lockForUpdate()
                        ->first();

                    if ($slot) {
                        $slot->update(['status' => 'available']);
                    }

                    $checkIn->addDay();
                }
            });
        } catch (\Exception $e) {
            return back()->with('error', 'キャンセル処理に失敗しました: ' . $e->getMessage());
        }

        // メール送信（失敗しても DB はキャンセル済みなので続行）
        try {
            $reservation->loadMissing('stayPlan');
            Mail::to($reservation->guest_email)->send(new ReservationCancelled($reservation));
        } catch (\Exception $e) {
            \Log::warning('キャンセルメール送信失敗: ' . $e->getMessage());
        }

        return back()->with('success', '予約をキャンセルし、予約枠を解放しました。');
    }
}