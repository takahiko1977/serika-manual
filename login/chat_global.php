<?php
session_start();
$selfId = $_SESSION['user_id'] ?? '1'; // ログイン中のID
$chatFile = "chat/global.txt"; // 全体チャット

// アップロード先ディレクトリ
$uploadDir = "uploads/global";
if (!is_dir($uploadDir)) {
    mkdir($uploadDir, 0777, true);
}

// メッセージ投稿処理
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $message = trim($_POST['message'] ?? "");
    $imagePath = "";

    // 画像アップロード
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

        $entry = "\n<br>{$date} [{$name}] : " . htmlspecialchars($message, ENT_QUOTES, 'UTF-8');
        if ($imagePath !== "") {
            $entry .= "<br><img src='{$imagePath}' style='max-width:200px; max-height:200px;'>";
        }
        $entry .= "<br>------------------------";

        file_put_contents($chatFile, $entry, FILE_APPEND);
    }
    header("Location: chat_global.php");
    exit;
}

// チャット履歴読み込み
$chatHistory = "";
if (file_exists($chatFile)) {
    $chatHistory = file_get_contents($chatFile);
}

?>
<!DOCTYPE html>
<html lang="ja">
<head>
  <meta charset="UTF-8">
  <title>全体チャット</title>
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
  <style>
    @media (max-width: 576px) {
      .card-body {
        height: 50vh !important;
        overflow-y: scroll;
      }
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
  <h3 class="mb-3 text-center">🌐 全体チャット</h3>

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

  <!-- 戻るボタン -->
  <div class="d-grid gap-2 mt-3">
    <a href="../index.php" class="btn btn-secondary">トップに戻る</a>
  </div>
</div>

</body>
</html>
