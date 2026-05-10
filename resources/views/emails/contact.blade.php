<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <style>
        body { font-family: 'Helvetica Neue', Arial, sans-serif; color: #333; line-height: 1.6; }
        .container { max-width: 600px; margin: 0 auto; padding: 20px; border: 1px solid #f0f0f0; border-radius: 12px; }
        .header { text-align: center; padding-bottom: 20px; border-bottom: 2px solid #6366f1; }
        .content { padding: 20px 0; }
        .info-table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        .info-table th { text-align: left; padding: 10px; background-color: #f8fafc; border-bottom: 1px solid #eee; width: 30%; }
        .info-table td { padding: 10px; border-bottom: 1px solid #eee; }
        .message-box { margin-top: 20px; padding: 15px; background-color: #f9fafb; border-radius: 8px; border: 1px solid #eee; white-space: pre-wrap; }
        .footer { text-align: center; font-size: 12px; color: #999; margin-top: 30px; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1 style="color: #444; font-size: 20px;">
                {{ $is_admin ? '【管理者通知】お問い合わせ届きました' : 'お問い合わせ受付完了' }}
            </h1>
        </div>

        <div class="content">
            @if($is_admin)
                <p>管理者様、新しいお問い合わせがありました。</p>
            @else
                <p><strong>{{ $inputs['name'] }} 様</strong></p>
                <p>この度はお問い合わせいただき、誠にありがとうございます。<br>
                以下の内容で受け付けました。担当者より改めてご連絡いたします。</p>
            @endif

            <table class="info-table">
                <tr>
                    <th>お名前</th>
                    <td>{{ $inputs['name'] }} 様</td>
                </tr>
                <tr>
                    <th>メールアドレス</th>
                    <td>{{ $inputs['email'] }}</td>
                </tr>
            </table>

            <p style="margin-top: 20px; font-weight: bold; font-size: 14px; color: #666;">お問い合わせ内容：</p>
            <div class="message-box">{!! nl2br(e($inputs['body'])) !!}</div>

            @if(!$is_admin)
                <p style="font-size: 12px; color: #999; margin-top: 20px;">※このメールはシステムからの自動返信です。</p>
            @endif
        </div>

        <div class="footer">
            <p>© {{ date('Y') }} {{ config('app.name') }}<br>
            TEL: 00-0000-0000</p>
        </div>
    </div>
</body>
</html>