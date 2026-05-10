<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Room;
use App\Models\ReservationSlot;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class ReservationSlotController extends Controller
{
    /**
     * 予約枠一覧の表示
     * 管理画面で現在の在庫状況を確認する
     */
    public function index()
    {
        // フォームの選択肢用に稼働中の部屋を取得
        $rooms = Room::where('is_active', true)->get();

        // 予約枠を日付順に取得（リレーションで部屋名も取得）
        $slots = ReservationSlot::with('room')
                    ->orderBy('date', 'asc')
                    ->paginate(50);

        return view('admin.slots.index', compact('rooms', 'slots'));
    }

    /**
     * 予約枠の一括作成
     * 指定期間 × 部屋数分のレコードを生成する
     */
    public function bulkStore(Request $request)
    {
        $request->validate([
            'room_id'    => 'required|exists:rooms,id',
            'start_date' => 'required|date|after_or_equal:today',
            'end_date'   => 'required|date|after_or_equal:start_date',
            'price'      => 'nullable|integer|min:0',
        ]);

        $room  = Room::findOrFail($request->room_id);
        $price = $request->filled('price') ? (int)$request->price : $room->price;
        $start = Carbon::parse($request->start_date);
        $end   = Carbon::parse($request->end_date);

        DB::transaction(function () use ($start, $end, $room, $price) {
            for ($date = $start->copy(); $date->lte($end); $date->addDay()) {
                for ($i = 0; $i < $room->number_of_rooms; $i++) {
                    ReservationSlot::create([
                        'room_id' => $room->id,
                        'date'    => $date->toDateString(),
                        'price'   => $price,
                        'status'  => 'available',
                    ]);
                }
            }
        });

        return redirect()->back()->with('success', '予約枠を一括作成しました。');
    }

    /**
     * 予約枠の編集
     * 日付・部屋・料金・ステータスを編集可能
     */
    public function edit(ReservationSlot $slot)
    {
        $rooms = Room::where('is_active', true)->get();
        return view('admin.slots.edit', compact('slot', 'rooms'));
    }

    /**
     * 予約枠の更新
     * 日付・部屋・料金・ステータスを更新
     */
    public function update(Request $request, ReservationSlot $slot)
    {
        $validated = $request->validate([
            'date'    => 'required|date',
            'room_id' => 'required|exists:rooms,id',
            'price'   => 'required|integer|min:0',
            'status'  => 'required|in:available,reserved',
        ]);

        $slot->update($validated);

        return redirect()->route('admin.slots.index')->with('success', '予約枠を更新しました。');
    }

    /**
     * 予約枠の削除
     * サイト外予約（電話等）が入った際に在庫を減らすための処理
     */
    public function destroy(ReservationSlot $slot)
    {
        // 既に予約済みの枠を誤って消さないためのチェック
        if ($slot->status === 'reserved') {
            return redirect()->back()->with('error', '予約済みの枠は削除できません。');
        }

        $slot->delete();

        return redirect()->back()->with('success', '予約枠を削除しました（在庫を1減らしました）。');
    }
}