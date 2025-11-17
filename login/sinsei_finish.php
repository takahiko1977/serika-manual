<?php
session_start();

// セッションチェック
if (!isset($_SESSION['new_user'])) {
    header("Location: login_new.php");
    exit;
}

// フォームデータ取得
$bank = $_POST['bank'] ?? '';
$name = $_POST['name'] ?? '';

if (empty($bank) || empty($name)) {
    echo "口座情報が入力されていません。";
    exit;
}

// セッションからユーザー情報取得
$id = $_SESSION['new_user']['id'];
$pwd = $_SESSION['new_user']['pwd'];
$mail = $_SESSION['new_user']['mail'];

// データ整形
$clean = fn($v) => str_replace(["\r", "\n"], "", trim($v));
$id = $clean($id);
$pwd = $clean($pwd);
$mail = $clean($mail);
$bank = $clean($bank);
$name = $clean($name);

$dir = __DIR__ . "/user/";
$addFile = $dir . "add.txt";
$sinseiFile = $dir . "sinsei.txt";

// データ形式
$userData = "<user><id>{$id}</id><pwd>{$pwd}</pwd><mail>{$mail}</mail></user>\n";
$sinseiData = "<sinsei><id>{$id}</id><bank>{$bank}</bank><name>{$name}</name></sinsei>\n";

// 既存データチェック
$exists = file_exists($addFile) ? file_get_contents($addFile) : '';

if (strpos($exists, $id) === false) {
    file_put_contents($addFile, $userData, FILE_APPEND);
    file_put_contents($sinseiFile, $sinseiData, FILE_APPEND);
    echo "申請が完了しました。振込確認をお待ちください。";
} else {
    echo "すでに申請済みのIDです。";
}

// セッション削除
unset($_SESSION['new_user']);
session_destroy();
?>
