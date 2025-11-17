<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: text/plain; charset=utf-8");

// === パラメータ取得 ===
$id = $_GET['id'] ?? '';
$pwd = $_GET['pwd'] ?? '';
$filename = $_GET['filename'] ?? '';
$data = $_GET['data'] ?? '';

if ($id === '' || $pwd === '' || $filename === '' || $data === '') {
    echo "ERROR: missing parameters";
    exit;
}

// === 認証チェック ===
$authUrl = "http://serika.cloudfree.jp/axwork/login/login_api.php?id=" . urlencode($id) . "&pwd=" . urlencode($pwd);
$authResult = @file_get_contents($authUrl);

if (trim($authResult) !== "OK") {
    echo "ERROR: authentication failed";
    exit;
}

// === ディレクトリ作成（ユーザーごと） ===
$dir = __DIR__ . "/dir/" . $id;
if (!is_dir($dir)) {
    mkdir($dir, 0777, true);
}

// === 日付・時間・分・秒をファイル名に追加 ===
$dateTime = date("Y-m-d_H"); // 例: 2025-11-06_14-30-59
$baseFilename = basename($filename);
$newFilename = "{$dateTime}__{$baseFilename}"; // 日付＋時間をファイル名に追加

// === ファイル保存パス ===
$savePath = $dir . "/" . $newFilename;

// --- Base64復号 ---
$decoded = base64_decode($data, true);

// --- Base64が壊れている場合 ---
if ($decoded === false) {
    // データが途中で切れたとき用に、そのまま保存もしておく
    file_put_contents($savePath . ".error.txt", $data);
    echo "ERROR: base64 decode failed";
    exit;
}

// === 古いファイルを削除（最新3つを残す） ===
$files = glob($dir . "/{$dateTime}__*.csv"); // 日付＋時間付きのファイルを取得
usort($files, function($a, $b) {
    $dateA = substr(basename($a), 0, 19); // 日付部分＋時間部分を抽出 (例: 2025-11-06_14-30-59)
    $dateB = substr(basename($b), 0, 19); // 日付部分＋時間部分を抽出
    return strtotime($dateB) - strtotime($dateA); // 降順ソート
});

// 最新3つを残す
$filesToDelete = array_slice($files, 3); // 最新3つ以外を選択

foreach ($filesToDelete as $file) {
    if (unlink($file)) {
        echo "削除成功: " . basename($file) . "\n";
    } else {
        echo "削除失敗: " . basename($file) . "\n";
    }
}

// --- 追記モードで保存（分割対応）---
if (file_put_contents($savePath, $decoded, FILE_APPEND) !== false) {
    echo "OK";
} else {
    echo "ERROR: failed to save";
}
?>
