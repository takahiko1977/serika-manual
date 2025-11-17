<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <title>チャットルーム: <?= htmlspecialchars($room['room_name']) ?></title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        #chat-box {
            border: 1px solid #ccc; 
            padding: 10px; 
            height: 50vh; /* 画面の半分の高さ */
            overflow-y: scroll; 
            background: #f9f9f9;
        }
        .message { margin-bottom: 10px; }
        .sender { font-weight: bold; }
        .time { font-size: 0.8em; color: gray; }
    </style>
</head>
<body class="bg-light">

<div class="container py-4">
    <h2 class="text-center mb-3">チャットルーム: <?= htmlspecialchars($room['room_name']) ?></h2>

    <div id="chat-box" class="mb-3">
        <?php foreach ($messages as $msg): ?>
            <div class="message">
                <span class="sender"><?= htmlspecialchars($msg['name']) ?>:</span>
                <span><?= htmlspecialchars($msg['message']) ?></span>
                <span class="time">(<?= $msg['created_at'] ?>)</span>
            </div>
        <?php endforeach; ?>
    </div>

    <form method="post" class="d-flex flex-column flex-sm-row gap-2 mb-3">
        <input type="text" name="message" class="form-control" placeholder="メッセージ入力" required>
        <button type="submit" class="btn btn-primary w-100 w-sm-auto">送信</button>
    </form>

    <div class="d-grid gap-2">
        <a href="top.php" class="btn btn-secondary w-100">ログインに戻る</a>
    </div>

    <div class="d-grid gap-2">
        <a href="../index.php" class="btn btn-secondary w-100">トップに戻る</a>
    </div>
</div>

<script>
    // チャット自動スクロール
    var chatBox = document.getElementById('chat-box');
    chatBox.scrollTop = chatBox.scrollHeight;
</script>
</body>
</html>
