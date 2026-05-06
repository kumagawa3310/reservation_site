@if($is_admin)
    <p>【管理者通知】</p>
    <p><strong>送信者名:</strong> {{ $inputs['name'] }} 様</p>
@else
    <p><strong>{{ $inputs['name'] }} 様</strong></p>
    <p>この度はお問い合わせいただき、誠にありがとうございます。</p>
    <p>※このメールはシステムからの自動返信です。</p>
    <p>以下の内容でお問い合わせを受け付けました。</p>
    <p>担当者よりご連絡いたしますので今しばらくお待ちくださいませ。</p>
@endif

<hr>
<p><strong>メールアドレス:</strong> {{ $inputs['email'] }}</p>
<p><strong>内容:</strong></p>
<p>{!! nl2br(e($inputs['body'])) !!}</p>
<hr>