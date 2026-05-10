<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Reservation;
use App\Mail\ReminderEmail;
use Illuminate\Support\Facades\Mail;
use Carbon\Carbon;

class SendReminderEmail extends Command
{
    // 実行するコマンド名
    protected $signature = 'app:send-reminders';
    protected $description = '宿泊3日前の予約者にリマインドメールを送信します';

    public function handle()
    {
        // 3日後の日付を取得
        $targetDate = Carbon::today()->addDays(3);

        // 対象の予約（確定済み status=1）を取得
        $reservations = Reservation::whereDate('check_in_date', $targetDate)
            ->where('status', 1)
            ->get();

        foreach ($reservations as $reservation) {
            try {
                Mail::to($reservation->guest_email)->send(new ReminderEmail($reservation));
                $this->info("Sent reminder to: {$reservation->guest_email}");
            } catch (\Exception $e) {
                $this->error("Failed to send: {$reservation->id}. Error: {$e->getMessage()}");
            }
        }
    }
}

