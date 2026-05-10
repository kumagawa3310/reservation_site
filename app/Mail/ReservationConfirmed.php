<?php

namespace App\Mail;

use App\Models\Reservation;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class ReservationConfirmed extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public Reservation $reservation,
        public string $type // 'customer' or 'admin'
    ) {}

    public function envelope(): Envelope
    {
        $subject = $this->type === 'admin' 
            ? "【通知】新規予約が入りました（予約番号:#{$this->reservation->id}）" 
            : "【{$this->reservation->stayPlan->name}】ご予約が確定しました";

        return new Envelope(subject: $subject);
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.reservation_confirmed',
        );
    }
}