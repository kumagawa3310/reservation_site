<?php

namespace App\Http\Controllers;

use App\Models\StayPlan;
use App\Models\ReservationSlot;
use Illuminate\Http\Request;
use Carbon\Carbon;

class PlanController extends Controller
{
    public function index(Request $request)
    {
        // 1. クエリビルダを開始（部屋が稼働中のものに限定）
        $query = StayPlan::whereHas('room', function ($q) {
            $q->where('is_active', true);
        });

        // 2. キーワード検索（プラン名または説明文）
        if ($request->filled('keyword')) {
            $keyword = $request->input('keyword');
            $query->where(function($q) use ($keyword) {
                $q->where('name', 'like', "%{$keyword}%")
                ->orWhere('description', 'like', "%{$keyword}%");
            });
        }

        // 3. 価格検索
        if ($request->filled('max_price')) {
            $query->where('price', '<=', $request->input('max_price'));
        }

        // 4. 結果取得（最新順）
        $plans = $query->with('room')->latest()->get();

        return view('plans.index', compact('plans'));
    }

    public function show(Request $request, StayPlan $plan)
    {
        if (!$plan->room->is_active) {
            abort(404);
        }

        // URLパラメータから 'month' を取得（例: 2026-06）。なければ今月。
        $monthParam = $request->query('month');
        try {
            $date = $monthParam ? Carbon::parse($monthParam . '-01') : Carbon::today();
        } catch (\Exception $e) {
            $date = Carbon::today();
        }

        // 前月・次月のリンク用データ
        $prevMonth = $date->copy()->subMonth()->format('Y-m');
        $nextMonth = $date->copy()->addMonth()->format('Y-m');

        // カレンダーの範囲計算
        $firstDay = $date->copy()->firstOfMonth();
        $lastDay = $date->copy()->lastOfMonth();
        $startDay = $firstDay->copy()->startOfWeek(Carbon::SUNDAY);
        $endDay = $lastDay->copy()->endOfWeek(Carbon::SATURDAY);

        // 予約枠の取得（その部屋・その期間分）
        $slots = ReservationSlot::where('room_id', $plan->room_id)
            ->whereBetween('date', [$startDay->format('Y-m-d'), $endDay->format('Y-m-d')])
            ->get()
            ->keyBy(fn($item) => Carbon::parse($item->date)->format('Y-m-d'));

        $calendar = [];
        $currentDay = $startDay->copy();

        while ($currentDay <= $endDay) {
            $dateString = $currentDay->format('Y-m-d');
            $slot = $slots->get($dateString);

            $isPast = $currentDay->isPast() && !$currentDay->isToday();
            $isAvailable = (!$isPast && $slot && $slot->status === 'available');

            $calendar[] = [
                'date' => $currentDay->copy(),
                'is_current_month' => $currentDay->month === $date->month,
                'is_past' => $isPast,
                'status' => $isAvailable ? '○' : ($isPast ? '-' : '×'),
                'is_available' => $isAvailable,
            ];
            $currentDay->addDay();
        }

        return view('plans.show', compact('plan', 'calendar', 'date', 'prevMonth', 'nextMonth'));
    }
}