<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class ContactMessage extends Mailable
{
    use Queueable, SerializesModels;

    /** @var array<string, string> */
    public array $inputs;
    public bool $is_admin;

    /**
     * @param array<string, string> $inputs
     */
    public function __construct(array $inputs, bool $is_admin = true)
    {
        $this->inputs = $inputs;
        $this->is_admin = $is_admin;
    }

    public function envelope(): Envelope
    {
        $subject = $this->is_admin
            ? $this->inputs['name'] . '様からのお問い合わせ'
            : 'お問い合わせ完了';

        return new Envelope(
            subject: $subject,
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.contact',
        );
    }
    // ...以下そのまま
}