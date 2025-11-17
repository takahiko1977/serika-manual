<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: text/plain; charset=utf-8");

// === パラメータ ===
$id       = $_GET['id']       ?? '';
$pwd      = $_GET['pwd']      ?? '';
$filename = $_GET['filename'] ?? '';
$data     = $_GET['data']     ?? '';
$mkdirReq = $_GET['mkdir']    ?? '';

if ($id === '' || $pwd === '') {
    echo "ERROR: missing id/pwd";
    exit;
}

// === 認証チェック ===
$authUrl = "http://serika.cloudfree.jp/axwork/login/login_api.php?id=" 
          . urlencode($id) . "&pwd=" . urlencode($pwd);
$authResult = @file_get_contents($authUrl);

if (trim($authResult) !== "OK") {
    echo "ERROR: authentication failed";
    exit;
}

// === 基本パス ===
$baseDir = __DIR__ . "/../login/apps";
$userDir = $baseDir . "/" . preg_replace('/[^\w\-._]/u', '_', $id);
if (!is_dir($userDir)) mkdir($userDir, 0777, true);

// === mkdirモード ===
if ($mkdirReq !== '') {
    $target = $userDir . "/" . str_replace(["..", "\\"], "", $mkdirReq);
    if (!is_dir($target)) {
        mkdir($target, 0777, true);
        echo "OK (mkdir)";
    } else {
        echo "OK (exists)";
    }
    exit;
}

// === アップロードモード ===
if ($filename === '' || $data === '') {
    echo "ERROR: missing filename/data";
    exit;
}

// === appslist.txt はスキップ ===
if (basename($filename) === "appslist.txt_part0") {
    echo "OK (skip appslist.txt)";
    exit;
}

// === Base64デコード ===
$decoded = base64_decode($data, true);
if ($decoded === false) {
    echo "ERROR: base64 decode failed";
    exit;
}

// === 保存パス生成 ===
$saveRelPath = str_replace(["..", "\\"], "", $filename);
$saveRelPath = str_replace("__", "/", $saveRelPath);

// 実際の保存先
$saveDir = dirname($userDir . "/" . $saveRelPath);
if (!is_dir($saveDir)) mkdir($saveDir, 0777, true);

$basename = basename($saveRelPath);
$savePath = $saveDir . "/" . $basename;

// === ファイル保存 ===
if (file_put_contents($savePath, $decoded, FILE_APPEND) === false) {
    echo "ERROR: failed to save";
    exit;
}

// === list.txt 更新 ===
// list.txt は「機能フォルダ」単位で作成する
$funcDir = dirname($savePath);
$listFile = $funcDir . "/list.txt";

// 機能フォルダ内からの相対パスに変換
$entry = str_replace($funcDir . "/", "", $savePath);

// 重複行チェックして追記
$alreadyExists = false;
if (file_exists($listFile)) {
    $lines = file($listFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    if (in_array($entry, $lines)) {
        $alreadyExists = true;
    }
}

if (!$alreadyExists) {
    file_put_contents($listFile, $entry . "\n", FILE_APPEND);
}

echo "OK";
?>
