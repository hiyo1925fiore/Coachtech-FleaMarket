<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>取引完了のお知らせ</title>
</head>
<body>
    <h2>取引が完了しました</h2>

    <p>{{ $exhibition->name }}の取引が完了しました。</p>

    <p>購入者があなたへの評価を送信しました。</p>
    <p>あなたも購入者への評価をお願いします。</p>

    <p>
        <a href="{{ route('chat.show', $exhibition->id) }}">チャット画面で評価する</a>
    </p>

    <p>COACHTECH フリマ</p>
</body>
</html>