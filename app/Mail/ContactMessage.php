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

    // プロパティを定義（publicにすると自動でViewに渡される）
    public $inputs;
    public $is_admin; // メッセージ分岐用

    public function __construct($inputs, $is_admin = true)
    {
        $this->inputs = $inputs;
        $this->is_admin = $is_admin; // メッセージ分岐用
    }

    public function envelope()
    {
    // 管理者か宿泊者かで件名を分ける
        $subject = $this->is_admin 
            ? $this->inputs['name'] . '様からのお問い合わせ' // 管理者向け
            : 'お問い合わせ完了';                            // 宿泊者向け

        return new Envelope(
            subject: $subject,
        );
    }

    public function content()
    {
        return new Content(
            view: 'emails.contact',
        );
    }
    // ...以下そのまま
}