<?php
header("Content-Type: text/plain; charset=UTF-8");

$id = $_GET['id'] ?? '';
$pwd = $_GET['pwd'] ?? '';

if ($id === '' || $pwd === '') {
    echo "NG"; // パラメータ不足
    exit;
}

$filename = "user/user.txt";
if (!file_exists($filename)) {
    echo "NG"; // ファイルがない
    exit;
}

$content = file_get_contents($filename);

// 正規表現で全ユーザーを抽出
preg_match_all('/<user>(.*?)<\/user>/s', $content, $matches);
$users = $matches[1];

$loginOK = false;

foreach ($users as $userData) {
    preg_match('/<id>(.*?)<\/id>/', $userData, $idMatch);
    preg_match('/<pwd>(.*?)<\/pwd>/', $userData, $pwdMatch);

    $fileId = $idMatch[1] ?? '';
    $filePwd = $pwdMatch[1] ?? '';

    if ($fileId === $id && $filePwd === $pwd) {
        $loginOK = true;
        break;
    }
}

// 結果出力
echo $loginOK ? "OK" : "NG";
