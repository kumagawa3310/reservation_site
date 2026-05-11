<?php

namespace App\Http\Controllers;

use App\Models\StayPlan;
use App\Models\Reservation;
use App\Models\ReservationSlot;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;
use Stripe\Stripe;
use Stripe\PaymentIntent;

class ReservationController extends Controller
{
    /**
     * 予約入力画面の表示
     */
    public function create(Request $request, StayPlan $plan)
    {
    // クエリパラメータから日付を取得
        $check_in = $request->query('check_in');
        $check_out = $request->query('check_out');

    // 日付が足りない場合はプラン詳細に戻す
        if (!$check_in || !$check_out) {
            return redirect()->route('plans.show', $plan)->with('error', '日程を選択してください。');
        }

    // 宿泊日数の計算
    $start = \Carbon\Carbon::parse($check_in);
    $end = \Carbon\Carbon::parse($check_out);
        $nights = $start->diffInDays($end);

        // 合計金額（基本料金 × 泊数）
        $total_price = $plan->price * $nights;

        // クライアント側に渡すためのStripe公開キー
        $stripe_key = config('services.stripe.key');

        return view('reservations.create', compact('plan', 'check_in', 'check_out', 'nights', 'stripe_key'));
    }

    /**
     * 予約実行（保存処理）
     */
    public function store(Request $request, StayPlan $plan)
    {
        $validated = $request->validate([
            'check_in'          => 'required|date|after_or_equal:today',
            'check_out'         => 'required|date|after:check_in',
            'guest_name'        => 'required|string|max:255',
            'guest_email'       => 'required|email|max:255',
            'number_of_guests'  => 'required|integer|min:1',
            'payment_method'    => 'required|in:on_site,credit_card', // 決済方法
            'payment_intent_id' => 'required_if:payment_method,credit_card', // クレカ時は必須
        ]);

        // 1. クレジットカード決済の場合、Stripe側での成功を検証
        $paymentStatus = 'pending'; // デフォルト：未支払い
        if ($validated['payment_method'] === 'credit_card') {
            try {
                Stripe::setApiKey(config('services.stripe.secret'));
                $intent = PaymentIntent::retrieve($validated['payment_intent_id']);

                if ($intent->status !== 'succeeded') {
                    return back()->withInput()->with('error', '決済が完了していません。再度お試しください。');
                }
                $paymentStatus = 'paid'; // 支払い済み
            } catch (\Exception $e) {
                Log::error('Stripe検証エラー: ' . $e->getMessage());
                return back()->withInput()->with('error', '決済情報の確認に失敗しました。');
            }
        }

        try {
            $reservation = DB::transaction(function () use ($validated, $plan, $paymentStatus) {

                // 2. 在庫確認（排他ロック）
                $exists = Reservation::where('room_id', $plan->room_id)
                    ->where('status', '!=', Reservation::STATUS_CANCELLED)
                    ->where('check_in_date', '<', $validated['check_out'])
                    ->where('check_out_date', '>', $validated['check_in'])
                    ->lockForUpdate()
                    ->exists();

                if ($exists) {
                    throw new \Exception('申し訳ございません。入れ違いでご希望の日程が埋まってしまいました。');
                }

                $checkIn  = Carbon::parse($validated['check_in']);
                $checkOut = Carbon::parse($validated['check_out']);
                $nights   = $checkIn->diffInDays($checkOut);

                // 合計金額の計算（プラン単価 × 泊数 × 人数）
                $calculated_total_price = $plan->price * $nights * $validated['number_of_guests'];

                // 3. 予約作成
                $reservation = Reservation::create([
                    'stay_plan_id'              => $plan->id,
                    'room_id'                   => $plan->room_id,
                    'user_id'                   => auth()->id(),
                    'check_in_date'             => $validated['check_in'],
                    'check_out_date'            => $validated['check_out'],
                    'guest_name'                => $validated['guest_name'],
                    'guest_email'               => $validated['guest_email'],
                    'number_of_guests'          => $validated['number_of_guests'],
                    'total_price'               => $calculated_total_price,
                    'payment_method'            => $validated['payment_method'],
                    'payment_status'            => $paymentStatus,
                    'stripe_payment_intent_id'  => $validated['payment_intent_id'] ?? null,
                    'status'                    => Reservation::STATUS_CONFIRMED,
                ]);

                // 4. 在庫（スロット）の確保
                $day = $checkIn->copy();
                while ($day->lt($checkOut)) {
                    $slot = ReservationSlot::where('room_id', $plan->room_id)
                        ->where('date', $day->toDateString())
                        ->where('status', 'available')
                        ->lockForUpdate()
                        ->first();

                    if (!$slot) {
                        throw new \Exception('指定日程のスロットが確保できませんでした。');
                    }

                    $slot->update(['status' => 'reserved']);
                    $day->addDay();
                }

                return $reservation;
            });

        } catch (\Exception $e) {
            // クレカ決済済みだが予約DB保存に失敗した場合のケア（本来は返金処理などを検討）
            if ($paymentStatus === 'paid') {
                Log::critical('【要確認】決済完了後に予約失敗: IntentID ' . $validated['payment_intent_id']);
            }
            return back()->withInput()->with('error', $e->getMessage());
        }

        // メール送信
        try {
            Mail::to($reservation->guest_email)->send(new \App\Mail\ReservationConfirmed($reservation, 'customer'));
            Mail::to(config('mail.from.address'))->send(new \App\Mail\ReservationConfirmed($reservation, 'admin'));
        } catch (\Exception $e) {
            // メール失敗はログに残すが予約フローは止めない
            \Log::warning('予約確認メール送信失敗: ' . $e->getMessage());
        }

        return redirect()->route('reservations.complete', $reservation);
    }

    /**
     * 予約完了画面
     */
    public function complete(Reservation $reservation)
    {
        // 自分の予約のみ閲覧可能
        if ($reservation->user_id !== auth()->id()) {
            abort(403);
        }

        $reservation->load('stayPlan.room');

        return view('reservations.complete', compact('reservation'));
    }
}