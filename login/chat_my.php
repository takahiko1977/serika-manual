<?php
session_start();
$selfId = $_SESSION['user_id'] ?? '1'; // ログイン中のID
$room = $_GET['room'] ?? '';
$chatFile = "chat/{$room}.txt";
$targetId = "";

// アップロード先ディレクトリ（チャットごとに分ける）
$uploadDir = "uploads/{$room}";
if (!is_dir($uploadDir)) {
    mkdir($uploadDir, 0777, true);
}

$pos = strpos($room, "/");
$targetId = substr($room, 0, $pos);

//データから、指定タグの内容を取り出す
function getTagData(string $tag, string $data) {
    $st = "<".$tag.">";
    $en = "</".$tag.">";
    $pos1 = strpos($data, $st);
    $pos1 = strpos($data, ">", $pos1);
    $pos1 = $pos1 + 1;
    $pos2 = strpos($data, $en);
    $pos2 = $pos2 - $pos1;
    $dekita = substr($data, $pos1, $pos2);
    return $dekita;
}

//データから、文字列の存在する、指定タグの内容を取り出す
function getSearchTagData(string $tag, string $search, string $data) {
    $st = "<".$tag.">";
    $en = "</".$tag.">";
    $pos = strpos($data, $search);
    $pos1 = strrpos($data, $pos);
    $pos1 = strpos($data, ">", $pos1);
    $pos1 = $pos1 + 1;
    $pos2 = strpos($data, $en, $pos);
    $dekita = substr($data, $pos1, $pos2);
    return $dekita;
}

// メッセージ投稿処理
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $message = trim($_POST['message'] ?? "");
    $imagePath = "";

    // 画像アップロード処理
    if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
        $ext = pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION);
        $filename = uniqid("img_") . "." . $ext;
        $targetFile = $uploadDir . "/" . $filename;

        if (move_uploaded_file($_FILES['image']['tmp_name'], $targetFile)) {
            $imagePath = $targetFile;
        }
    }

    if ($message !== "" || $imagePath !== "") {
        $nameFile = "profile/{$selfId}/name.txt";
        $name = file_exists($nameFile) ? trim(file_get_contents($nameFile)) : $selfId;
        $date = date("Y.m.d H:i");

        $entry = "\n<br>"."{$date} [{$name}] : " . htmlspecialchars($message, ENT_QUOTES, 'UTF-8');
        if ($imagePath !== "") {
            $entry .= "<br><img src='{$imagePath}' style='max-width:200px; max-height:200px;'>";
        }
        $entry .= "<br>------------------------";

        file_put_contents($chatFile, $entry, FILE_APPEND);
    }
    header("Location: chat_my.php?room={$room}");
    exit;
}

// チャット履歴を読み込み（HTMLタグそのまま出す）
$chatHistory = "";
if (file_exists($chatFile)) {
    $chatHistory = file_get_contents($chatFile);
}

// 相手プロフィール取得
$targetName = "";
$targetComment = "";
$targetImage = "";
if ($targetId !== "" && is_dir("profile/{$targetId}")) {
    $nameFile = "profile/{$targetId}/name.txt";
    $commentFile = "profile/{$targetId}/comment.txt";
    $imageFile = "profile/{$targetId}/image.txt";

    if (file_exists($nameFile)) {
        $targetName = trim(file_get_contents($nameFile));
    }
    if (file_exists($commentFile)) {
        $targetComment = trim(file_get_contents($commentFile));
    }
    if (file_exists($imageFile)) {
        $imageName = trim(file_get_contents($imageFile));
        if ($imageName !== "") {
            $targetImage = "profile/{$targetId}/".$imageName;
        }
    }
}

?>
<!DOCTYPE html>
<html lang="ja">
<head>
  <meta charset="UTF-8">
  <title>チャット</title>
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
  <style>
    /* スマホ表示用調整 */
    @media (max-width: 576px) {
      /* チャット履歴の高さを画面半分に */
      .card-body {
        height: 50vh !important;
        overflow-y: scroll;
      }
      /* フォームの縦並び */
      form.d-flex {
        flex-direction: column !important;
      }
      form.d-flex input,
      form.d-flex button {
        width: 100% !important;
        margin-bottom: 5px;
      }
      form.d-flex input[type="file"] {
        max-width: 100% !important;
      }
    }
  </style>
</head>
<body class="bg-light">

<div class="container py-4">
  <h3 class="mb-3 text-center">チャットルーム (<?= htmlspecialchars($room) ?>)</h3>

  <!-- チャット履歴 -->
  <div class="card mb-3">
    <div class="card-body" style="height:300px; overflow-y:scroll; background:#f9f9f9;">
      <?= $chatHistory ?>
    </div>
  </div>

  <!-- メッセージ送信フォーム -->
  <form method="post" enctype="multipart/form-data" class="d-flex mb-4 gap-2">
    <input type="text" name="message" class="form-control" placeholder="メッセージを入力">
    <input type="file" name="image" accept="image/*" class="form-control" style="max-width:200px;">
    <button type="submit" class="btn btn-primary" style="width:100px;">送信</button>
  </form>

  <!-- 相手のプロフィール -->
  <?php if ($targetName): ?>
    <div class="card">
      <div class="card-body">
        <div class="mb-3">
            <?php if ($targetImage): ?>
                <img src="<?= htmlspecialchars($targetImage) ?>" alt="プロフィール写真" 
                    class="rounded-circle img-fluid" style="width:120px; height:120px;">
            <?php endif; ?>
        </div>
        <h5 class="card-title"><?= htmlspecialchars($targetName) ?></h5>
        <p class="card-text"><?= nl2br(htmlspecialchars($targetComment)) ?></p>
      </div>
    </div>
  <?php endif; ?>

  <!-- ボタン -->
  <div class="d-grid gap-2 mt-3">
    <a href="chat_list.php" class="btn btn-primary">チャットルーム一覧に戻る</a>
    <a href="../index.php" class="btn btn-secondary">トップに戻る</a>
  </div>
</div>

</body>
</html>
