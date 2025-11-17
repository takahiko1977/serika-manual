<?php
session_start();
$selfId = $_SESSION['user_id'] ?? '1'; // 仮にログイン中のIDが 1 とする

$roomDir = "chat/".$selfId;
if (!is_dir($roomDir)) {
    mkdir($roomDir, 0777, true);
}

// ファイルの場所
$roomListFile = "chat/".$selfId."/roomlist.txt";
$roomListFileOther = "chat/".$selfId."/otherlist.txt";

// 初期化
$myRooms = [];
$invitedRooms = [];

// 自分のルーム取得
if (file_exists($roomListFile)) {
    $content = file_get_contents($roomListFile);
    preg_match_all('/<room>(.*?)<\/room>/s', $content, $matches);
    foreach ($matches[1] as $roomData) {
        preg_match('/<name>(.*?)<\/name>/', $roomData, $nameMatch);
        $roomName = $nameMatch[1] ?? '';
        preg_match('/<user>(.*?)<\/user>/', $roomData, $userMatch);
        $userId = $userMatch[1] ?? '';
        if ($userId !== '') {
            $myRooms[] = ['room' => $roomName, 'user' => $userId];
        }
    }
}

// 招待ルーム取得
if (file_exists($roomListFileOther)) {
    $content = file_get_contents($roomListFileOther);
    preg_match_all('/<room>(.*?)<\/room>/s', $content, $matches);
    foreach ($matches[1] as $roomData) {
        preg_match('/<name>(.*?)<\/name>/', $roomData, $nameMatch);
        $roomName = $nameMatch[1] ?? '';
        preg_match('/<user>(.*?)<\/user>/', $roomData, $userMatch);
        $userId = $userMatch[1] ?? '';
        if ($userId !== '') {
            $invitedRooms[] = ['room' => $roomName, 'user' => $userId];
        }
    }
}
?>

<!DOCTYPE html>
<html lang="ja">
<head>
  <meta charset="UTF-8">
  <title>チャットルーム一覧</title>
  <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
  <style>
    .list-group-item {
      word-break: break-word;
    }

    /* span・ボタン両方の改行を制御 */
    .list-group-item span,
    .list-group-item a {
      white-space: nowrap;
    }

    /* モバイルでは縦並びでOK */
    @media (max-width: 767px) {
      .list-group-item {
        display: flex;
        flex-direction: column;
        align-items: stretch;
      }
      .list-group-item a {
        width: 100%;
        margin-top: 5px;
      }
    }

    /* PCでは横並び */
    @media (min-width: 768px) {
      .list-group-item {
        display: flex;
        flex-direction: row;
        justify-content: space-between;
        align-items: center;
        flex-wrap: nowrap;
      }
      .list-group-item a {
        width: auto !important; /* w-100を無効化 */
        flex-shrink: 0; /* ボタンが潰れないように */
      }
    }
  </style>
</head>
<body class="bg-light">

<div class="container py-4">
  <h2 class="mb-4 text-center">チャットルーム一覧</h2>

  <!-- 自分が主催しているルーム -->
  <h4>自分が主催しているルーム</h4>
  <ul class="list-group mb-4">
    <?php if (empty($myRooms)): ?>
      <li class="list-group-item text-muted">まだルームはありません</li>
    <?php else: ?>
      <?php foreach ($myRooms as $room): ?>
        <li class="list-group-item d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center">
          <span class="mb-2 mb-md-0"><?= htmlspecialchars($room['user']) ?> さんとのルーム</span>
          <a href="chat_my.php?room=<?= $selfId."/".urlencode($room['room']) ?>" class="btn btn-sm btn-primary w-100 w-md-auto">入室</a>
        </li>
      <?php endforeach; ?>
    <?php endif; ?>
  </ul>

  <!-- 他人が自分を追加しているルーム -->
  <h4>相手が自分を追加しているルーム</h4>
  <ul class="list-group mb-4">
    <?php if (empty($invitedRooms)): ?>
      <li class="list-group-item text-muted">まだ招待はありません</li>
    <?php else: ?>
      <?php foreach ($invitedRooms as $room): ?>
       <li class="list-group-item d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center">
         <span class="mb-2 mb-md-0"><?= htmlspecialchars($room['user']) ?> さんとのルーム</span>
         <a href="chat_my.php?room=<?= $selfId."/".urlencode($room['room']) ?>" class="btn btn-sm btn-primary w-100 w-md-auto">入室</a>
       </li>
      <?php endforeach; ?>
    <?php endif; ?>
  </ul>

  <!-- ボタン -->
  <div class="d-grid gap-2">
    <a href="chat_room_create.php" class="btn btn-primary">ルームを作る</a>
    <a href="../index.php" class="btn btn-secondary">トップに戻る</a>
  </div>
</div>

</body>
</html>
