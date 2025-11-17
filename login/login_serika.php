<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);

header("Content-Type: text/plain; charset=UTF-8");

require_once 'user.php'; // ユーザー関連関数

$id  = $_GET['id']  ?? '';
$pwd = $_GET['pwd'] ?? '';
$key = $_GET['key'] ?? '';

if ($id === '' || $pwd === '' || $key === '') {
    echo "NG";
    exit;
}

// === 実ディレクトリパス ===
$dir = __DIR__ . "/user";
if (!is_dir($dir)) {
    mkdir($dir, 0777, true); // フォルダがなければ作成
}

$filename = $dir . "/key.txt";

// === ファイルがなければ作成 ===
if (!file_exists($filename)) {
    file_put_contents($filename, "");
    echo "OK (file created)";
    exit;
}

// === キー確認と登録 ===
if (!isKeyExist($key, $filename)) {
    addKeyData($filename, $id, $pwd, $key);
    echo "OK (key added)";
    exit;
}

// === ログイン確認 ===
$content = file_get_contents($filename);
preg_match_all('/<user>(.*?)<\/user>/s', $content, $matches);
$users = $matches[1];
$loginOK = false;

foreach ($users as $userData) {
    preg_match('/<id>(.*?)<\/id>/', $userData, $idMatch);
    preg_match('/<pwd>(.*?)<\/pwd>/', $userData, $pwdMatch);
    preg_match('/<key>(.*?)<\/key>/', $userData, $keyMatch);

    $fileId = $idMatch[1] ?? '';
    $filePwd = $pwdMatch[1] ?? '';
    $fileKey = $keyMatch[1] ?? '';

    if ($fileId === $id && $filePwd === $pwd && $fileKey === $key) {
        $loginOK = true;
        break;
    }
}

echo $loginOK ? "OK (login success)" : "NG (login failed)";

?>
