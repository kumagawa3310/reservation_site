<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use App\Mail\ContactMessage;
use App\Models\Contact;

class ContactController extends Controller
{
    public function create()
    {
        return view('contact.create'); // resources/views/contact/create.blade.php を表示
    }

    public function store(Request $request)
    {
        // バリデーション（入力チェック）
        $validated = $request->validate([
            'name'   => 'required|max:50',
            'body'    => 'required',
            'email'   => 'required|email',
        ]);

        // DBに保存
        Contact::create($validated);

        // 管理者にメール送信
        Mail::to('admin@example.com')->send(new ContactMessage($validated, true));
        // 問い合わせしたアドレス（宿泊者）に自動返信
        Mail::to($validated['email'])->send(new ContactMessage($validated, false));

        // 元の画面に戻ってメッセージを表示
        return back()->with('success', 'お問い合わせを受け付けました。自動返信メールをご確認ください。');
    }
}
