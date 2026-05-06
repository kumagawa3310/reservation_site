<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Room;
use App\Models\SlotGenerationRule;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Artisan;

class SlotGenerationRuleController extends Controller
{
    public function index(): View
    {
        $rules = SlotGenerationRule::with('room')->latest()->get();
        $rooms = Room::where('is_active', true)->get();

        return view('admin.slot-rules.index', compact('rules', 'rooms'));
    }

    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'room_id'        => ['required', 'exists:rooms,id'],
            'start_date'     => ['required', 'date', 'after_or_equal:today'],
            'end_date'       => ['required', 'date', 'after_or_equal:start_date'],
            'price_override' => ['nullable', 'integer', 'min:0'],
        ]);

        SlotGenerationRule::create([
            'room_id'        => $request->room_id,
            'start_date'     => $request->start_date,
            'end_date'       => $request->end_date,
            'price_override' => $request->filled('price_override') ? $request->price_override : null,
            'is_active'      => true,
        ]);

        return redirect()->route('admin.slot-rules.index')
            ->with('success', '自動生成ルールを追加しました。');
    }

    public function update(Request $request, SlotGenerationRule $slotRule): RedirectResponse
    {
        $request->validate([
            'start_date'     => ['required', 'date'],
            'end_date'       => ['required', 'date', 'after_or_equal:start_date'],
            'price_override' => ['nullable', 'integer', 'min:0'],
            'is_active'      => ['required', 'boolean'],
        ]);

        $slotRule->update([
            'start_date'     => $request->start_date,
            'end_date'       => $request->end_date,
            'price_override' => $request->filled('price_override') ? $request->price_override : null,
            'is_active'      => $request->is_active,
        ]);

        return redirect()->route('admin.slot-rules.index')
            ->with('success', 'ルールを更新しました。');
    }

    public function destroy(SlotGenerationRule $slotRule): RedirectResponse
    {
        $slotRule->delete();

        return redirect()->route('admin.slot-rules.index')
            ->with('success', 'ルールを削除しました。');
    }

    public function generate(): RedirectResponse
    {
        Artisan::call('slots:generate');
        $output = trim(Artisan::output());

        return redirect()->route('admin.slot-rules.index')
            ->with('success', "手動実行完了: {$output}");
    }
}
