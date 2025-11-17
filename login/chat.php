<?php
session_start();
$selfId = $_SESSION['user_id'] ?? '1'; // ログイン中のID
$room = $_GET['room'] ?? '';
$chatFile = "chat/{$room}.txt";
$targetId = "";

// 相手IDを roomlist.txt から取得
$roomListFile = "chat/{$selfId}/roomlist.txt";
if (file_exists($roomListFile)) {
    $data = file_get_contents($roomListFile);
    if (preg_match("/<room>\s*<name>{$room}<\/name>\s*<user>(.*?)<\/user>\s*<\/room>/s", $data, $m)) {
        $targetId = $m[1];
    }
}

// メッセージ投稿処理
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['message'])) {
    $message = trim($_POST['message']);
    if ($message !== "") {
        $nameFile = "profile/{$selfId}/name.txt";
        $name = file_exists($nameFile) ? trim(file_get_contents($nameFile)) : $selfId;
        $date = date("Y.m.d H:i");
        $entry = "{$date} [{$name}] : {$message}\n------------------------\n";
        file_put_contents($chatFile, $entry, FILE_APPEND);
    }
    header("Location: chat.php?room={$room}");
    exit;
}

// チャット履歴を読み込み
$chatHistory = "";
if (file_exists($chatFile)) {
    $chatHistory = nl2br(htmlspecialchars(file_get_contents($chatFile), ENT_QUOTES, 'UTF-8'));
}

// 相手プロフィール取得
$targetName = "";
$targetComment = "";
if ($targetId !== "" && is_dir("profile/{$targetId}")) {
    $nameFile = "profile/{$targetId}/name.txt";
    $commentFile = "profile/{$targetId}/comment.txt";
    if (file_exists($nameFile)) $targetName = trim(file_get_contents($nameFile));
    if (file_exists($commentFile)) $targetComment = trim(file_get_contents($commentFile));
}
?>
<!DOCTYPE html>
<html lang="ja">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1"> <!-- スマホ対応必須 -->
  <title>チャット</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
  <style>
    .chat-box {
      height: 60vh; /* 画面の6割の高さに調整 */
      overflow-y: auto;
      background: #f9f9f9;
      padding: 10px;
      border-radius: 8px;
    }
  </style>
</head>
<body class="bg-light">

<div class="container py-3">
  <h3 class="mb-3 text-center">チャットルーム (<?= htmlspecialchars($room) ?>)</h3>

  <!-- チャット履歴 -->
  <div class="card mb-3">
    <div class="card-body chat-box">
      <?= $chatHistory ?>
    </div>
  </div>

  <!-- メッセージ送信フォーム -->
  <form method="post" class="d-flex gap-2 mb-4">
    <input type="text" name="message" class="form-control" placeholder="メッセージを入力">
    <button type="submit" class="btn btn-primary flex-shrink-0">送信</button>
  </form>

  <!-- 相手のプロフィール -->
  <?php if ($targetName): ?>
    <div class="card mb-3">
      <div class="card-body">
        <h5 class="card-title"><?= htmlspecialchars($targetName) ?></h5>
        <p class="card-text"><?= nl2br(htmlspecialchars($targetComment)) ?></p>
      </div>
    </div>
  <?php endif; ?>

  <!-- ボタン -->
  <div class="d-grid gap-2">
    <a href="chat_list.php" class="btn btn-primary">チャットルーム一覧に戻る</a>
    <a href="../index.php" class="btn btn-secondary">トップに戻る</a>
  </div>
</div>

</body>
</html>
