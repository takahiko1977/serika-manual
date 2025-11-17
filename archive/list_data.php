<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: text/plain; charset=utf-8");

// === パラメータ取得 ===
$id = $_GET['id'] ?? '';
$pwd = $_GET['pwd'] ?? '';

if ($id === '' || $pwd === '') {
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

// === 対象ディレクトリ ===
$dirPath = __DIR__ . "/dir/" . $id;

if (!is_dir($dirPath)) {
    echo "ERROR: directory not found";
    exit;
}

// === ファイル一覧取得 ===
$files = scandir($dirPath);
$list = [];

foreach ($files as $f) {
    if ($f === '.' || $f === '..') continue;
    $filePath = $dirPath . "/" . $f;
    if (is_file($filePath)) {
        $list[] = $f;
    }
}

// === 出力 ===
if (empty($list)) {
    echo "list: (no files)";
} else {
    echo "list:\n" . implode("\n", $list);
}
?>
