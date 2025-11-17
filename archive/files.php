<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: text/html; charset=utf-8");

// === 認証 ===
$id = $_GET['id'] ?? '';
$pwd = $_GET['pwd'] ?? '';

if ($id == '' || $pwd == '') {
    echo "<p style='color:red'>ERROR: id/pwd missing</p>";
    exit;
}

$authUrl = "http://localhost/axwork/login/login_api.php?id=" . urlencode($id) . "&pwd=" . urlencode($pwd);
$authResult = @file_get_contents($authUrl);

if (trim($authResult) !== "OK") {
    echo "<p style='color:red'>ERROR: authentication failed</p>";
    exit;
}

// === ファイル一覧 ===
$baseDir = __DIR__ . "/dir";
$userDir = $baseDir . "/" . preg_replace('/[^\w\-._]/u', '_', $id);

if (!is_dir($userDir)) {
    echo "<p>フォルダがまだありません。</p>";
    exit;
}

// === 再帰的にファイル一覧を取得 ===
function listFiles($dir, $baseUrl) {
    $allowed = ['jpg','jpeg','png','gif','pdf','txt','set','zip','rar','mp3','mp4','csv'];
    $items = scandir($dir);
    echo "<ul>";
    foreach ($items as $item) {
        if ($item === '.' || $item === '..') continue;
        $path = "$dir/$item";
        $url  = "$baseUrl/" . rawurlencode($item);
        if (is_dir($path)) {
            echo "<li><strong>📁 $item</strong>";
            listFiles($path, $url);
            echo "</li>";
        } else {
            $ext = strtolower(pathinfo($item, PATHINFO_EXTENSION));
            if (in_array($ext, $allowed)) {
                echo "<li><a href='$url' target='_blank'>📄 $item</a></li>";
            }
        }
    }
    echo "</ul>";
}

// === 表示 ===
echo "<h2>ファイル一覧（ユーザー: " . htmlspecialchars($id, ENT_QUOTES, 'UTF-8') . "）</h2>";
$baseUrl = "./dir/" . rawurlencode($id);
listFiles($userDir, $baseUrl);

echo "<hr><a href='index.html'>← アップロード画面へ戻る</a>";
?>
