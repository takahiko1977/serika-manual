<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: text/plain; charset=utf-8");

// === パラメータ取得 ===
$id = $_GET['id'] ?? '';
$pwd = $_GET['pwd'] ?? '';
$filename = $_GET['filename'] ?? '';

if ($id === '' || $pwd === '' || $filename === '') {
    echo "ERROR: missing parameters";
    exit;
}

// === 認証チェック ===
$authUrl = "http://localhost/axwork/login/login_api.php?id=" . urlencode($id) . "&pwd=" . urlencode($pwd);
$authResult = @file_get_contents($authUrl);

if (trim($authResult) !== "OK") {
    echo "ERROR: authentication failed";
    exit;
}

// === ファイル読み込み ===
$filePath = __DIR__ . "/dir/" . $id . "/" . basename($filename);

if (!file_exists($filePath)) {
    echo "ERROR: file not found";
    exit;
}

$content = file_get_contents($filePath);

// === 出力 ===
echo "data:" . $content;
?>
