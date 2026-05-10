<?php

namespace App\Console\Commands;

use App\Models\ReservationSlot;
use App\Models\SlotGenerationRule;
use Carbon\Carbon;
use Illuminate\Console\Command;

class GenerateReservationSlots extends Command
{
    protected $signature = 'slots:generate';
    protected $description = '有効な自動生成ルールに基づいて予約枠を生成する';

    public function handle(): int
    {
        $today = Carbon::today();
        $rules = SlotGenerationRule::with('room')
            ->where('is_active', true)
            ->where('end_date', '>=', $today)
            ->get();

        if ($rules->isEmpty()) {
            $this->info('有効なルールがありません。');
            return Command::SUCCESS;
        }

        $total = 0;

        foreach ($rules as $rule) {
            $created = $this->generateForRule($rule, $today);
            $total += $created;
            $this->line("  [{$rule->room->name}] {$rule->start_date->format('Y/m/d')}〜{$rule->end_date->format('Y/m/d')} : {$created} 件生成");
        }

        $this->info("合計 {$total} 件の予約枠を生成しました。");
        return Command::SUCCESS;
    }

    private function generateForRule(SlotGenerationRule $rule, Carbon $today): int
    {
        $start = $rule->start_date->lt($today) ? $today : $rule->start_date;
        $price = $rule->effectivePrice();
        $created = 0;

        for ($date = $start->copy(); $date->lte($rule->end_date); $date->addDay()) {
            $dateStr = $date->toDateString();

            $existing = ReservationSlot::where('room_id', $rule->room_id)
                ->where('date', $dateStr)
                ->count();

            $missing = $rule->room->number_of_rooms - $existing;

            for ($i = 0; $i < $missing; $i++) {
                ReservationSlot::create([
                    'room_id' => $rule->room_id,
                    'date'    => $dateStr,
                    'status'  => 'available',
                    'price'   => $price,
                ]);
                $created++;
            }
        }

        return $created;
    }
}
