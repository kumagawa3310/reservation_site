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
        .footer { text-align: center; font-size: 12px; color: #999; margin-top: 30px; }
        .badge { display: inline-block; padding: 4px 8px; background: #6366f1; color: white; border-radius: 4px; font-size: 11px; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1 style="color: #444; font-size: 20px;">
                {{ $type === 'admin' ? '新規予約通知' : 'ご予約確認書' }}
            </h1>
        </div>

        <div class="content">
            @if($type === 'admin')
                <p>管理者様、新しい予約が入りました。詳細は以下の通りです。</p>
            @else
                <p>{{ $reservation->guest_name }} 様</p>
                <p>この度は当ホテルをご予約いただき、誠にありがとうございます。<br>
                以下の内容でご予約を承りましたので、ご確認ください。</p>
            @endif

            <table class="info-table">
                <tr>
                    <th>予約番号</th>
                    <td><strong>#{{ str_pad($reservation->id, 6, '0', STR_PAD_LEFT) }}</strong></td>
                </tr>
                <tr>
                    <th>プラン名</th>
                    <td>{{ $reservation->stayPlan->name }}</td>
                </tr>
                <tr>
                    <th>チェックイン</th>
                    <td>{{ $reservation->check_in_date->format('Y/m/d') }}</td>
                </tr>
                <tr>
                    <th>チェックアウト</th>
                    <td>{{ $reservation->check_out_date->format('Y/m/d') }}</td>
                </tr>
                <tr>
                    <th>宿泊者名</th>
                    <td>{{ $reservation->guest_name }} 様</td>
                </tr>
                <tr>
                    <th>合計金額</th>
                    <td style="color: #6366f1; font-weight: bold;">¥{{ number_format($reservation->total_price) }}（税込）</td>
                </tr>
            </table>

            @if($type === 'customer')
                <div style="margin-top: 30px; padding: 15px; background: #eef2ff; border-radius: 8px;">
                    <p style="margin: 0; font-size: 14px; color: #4f46e5;">
                        ※当日はフロントにてお名前をお申し付けください。<br>
                        お会いできるのを楽しみにしております。
                    </p>
                </div>
            @endif
        </div>

        <div class="footer">
            <p>© {{ date('Y') }} {{ config('app.name') }}<br>
            TEL: 00-0000-0000</p>
        </div>
    </div>
</body>
</html>