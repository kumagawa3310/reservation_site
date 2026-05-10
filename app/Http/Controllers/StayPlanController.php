<?php

namespace App\Http\Controllers;

use App\Models\StayPlan;
use App\Models\Room; // 部屋選択用
use Illuminate\Http\Request;

class StayPlanController extends Controller
{
    /**
     * プラン一覧
     */
    public function index()
    {
        // 部屋情報も一緒に取得してページネーション
        $plans = StayPlan::with('room')->latest()->paginate(20);
        
        return view('admin.plans.index', compact('plans'));
    }

    /**
     * 新規作成画面
     */
    public function create()
    {
        // プラン作成時に紐付ける部屋一覧を取得
        $rooms = Room::all();
        
        return view('admin.plans.create', compact('rooms'));
    }

    /**
     * 保存処理
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'room_id'        => 'required|exists:rooms,id',
            'name'           => 'required|string|max:255',
            'price'          => 'required|integer|min:0',
            'description'    => 'nullable|string',
            'available_from' => 'nullable|date',
            'available_to'   => 'nullable|date|after_or_equal:available_from',
        ]);

        StayPlan::create($validated);

        return redirect()->route('admin.plans.index')
            ->with('success', 'プランを登録しました。');
    }

    /**
     * 編集画面
     */
    public function edit(StayPlan $plan)
    {
        $rooms = Room::all();
        
        return view('admin.plans.edit', compact('plan', 'rooms'));
    }

    /**
     * 更新処理
     */
    public function update(Request $request, StayPlan $plan)
    {
        $validated = $request->validate([
            'room_id'        => 'required|exists:rooms,id',
            'name'           => 'required|string|max:255',
            'price'          => 'required|integer|min:0',
            'description'    => 'nullable|string',
            'available_from' => 'nullable|date',
            'available_to'   => 'nullable|date|after_or_equal:available_from',
        ]);

        $plan->update($validated);

        return redirect()->route('admin.plans.index')
            ->with('success', 'プランを更新しました。');
    }

    /**
     * 削除処理（論理削除）
     */
    public function destroy(StayPlan $plan)
    {
        $plan->delete();

        return redirect()->route('admin.plans.index')
            ->with('success', 'プランを削除しました。');
    }
}