<?php
session_start();

// ユーザーIDがログインしているか確認
if (!isset($_SESSION['user_id'])) {
    // die("ログインしてください");
}

$user_id = $_SESSION['user_id'];

// アップロードがあるか確認
if (isset($_FILES['profile_image']) && $_FILES['profile_image']['error'] === UPLOAD_ERR_OK) {

    // 保存先フォルダ
    $uploadDir = 'profile/' . $user_id . '/';
    if (!is_dir($uploadDir)) {
        mkdir($uploadDir, 0755, true); // フォルダ作成（再帰的に）
    }

    // 元のファイル名を取得し、安全な名前に変換
    $tmpName  = $_FILES['profile_image']['tmp_name'];
    $originalName = basename($_FILES['profile_image']['name']);
    $originalName = preg_replace("/[^a-zA-Z0-9\.\-_]/", "_", $originalName);

    // 拡張子を取得してユニークな名前に変更
    $fileExt = pathinfo($originalName, PATHINFO_EXTENSION);
    $fileName = uniqid('profile_') . "." . $fileExt;

    // 保存パス
    $targetFile = $uploadDir . $fileName;

    // MIMEタイプチェック（画像のみ許可）
    $allowedTypes = ['image/jpeg', 'image/png', 'image/gif'];
    if (!in_array(mime_content_type($tmpName), $allowedTypes)) {
        // die("許可されていないファイルタイプです");
    }

    // アップロード実行
    if (move_uploaded_file($tmpName, $targetFile)) {
        echo "アップロード成功: $fileName";
        $files = $uploadDir."image.txt";
        file_put_contents($files, $fileName);

        // ここでユーザーIDに紐付けて保存する例
        // 例: user.txt や DB にパスを保存
        // file_put_contents("profile/{$user_id}/image.txt", $targetFile);

        // $_SESSION['profile_image'] = $targetFile; // セッションに保存
    } else {
        echo "アップロード失敗";
    }

} else {
    echo "ファイルが選択されていないか、エラーがあります";
}
header("Location: profile_new.php");
