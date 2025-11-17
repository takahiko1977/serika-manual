<?php
$id   = $_GET['id'] ?? '';
$name   = $_GET['name'] ?? '';
$pwd  = $_GET['pwd'] ?? '';
$data = $_GET['data'] ?? '';

if ($id === '' || $pwd === '' || $data === '') {
    echo "ERROR: missing";
    exit;
}

// --- 認証 ---
$authUrl = "http://serika.cloudfree.jp/axwork/login/login_api.php?id=" . urlencode($id) . "&pwd=" . urlencode($pwd);
$auth = @file_get_contents($authUrl);
if (trim($auth) !== "OK") {
    echo "ERROR: auth";
    exit;
}

// --- デコード ---
$decoded = base64_decode($data);
if ($decoded === false) {
    echo "ERROR: base64 decode";
    exit;
}
$decoded = trim($decoded);

// --- ファイル ---
$dir  = __DIR__ . "/../login/apps";
$file = $dir . "/list.txt";

// --- ファイル初期化 ---
if (!file_exists($file)) {
    $initial = "<apps>\n</apps>\n";
    if (file_put_contents($file, $initial) === false) {
        echo "ERROR: cannot create list.txt";
        exit;
    }
}

// --- ファイル読み込み ---
$content = file_get_contents($file);
if ($content === false) {
    echo "ERROR: cannot read list.txt";
    exit;
}

// --- 追記する app の name を作成 ---
$appName = $name;

// --- 同じ <name> が存在するか確認 ---
if (strpos($content, "<name>$appName</name>") === false) {
    // --- 閉じタグ </apps> を削除して追記 ---
    $content = rtrim($content);
    $content = preg_replace('/<\/apps>$/', '', $content);

    $newApp = str_replace("<app>", "<app><name>$appName</name>", $decoded);
    $newApp .= "</apps>\n";
    
    // --- 追記 ---
    if (file_put_contents($file, $content . $newApp, LOCK_EX) === false) {
        echo "ERROR: append failed";
        exit;
    }
}

echo "OK";
?>
