<?php
// sinsei_approve.php （管理画面）

$dir = __DIR__ . '/user/';
$userFile = $dir . 'user.txt';
$addFile = $dir . 'add.txt';
$sinseiFile = $dir . 'sinsei.txt';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['approve'])) {
    $id = $_POST['id'];

    // --- add.txtから該当ユーザーを探す ---
    $addContent = file_exists($addFile) ? file_get_contents($addFile) : '';
    $patternUser = '/<user>\s*<id>' . preg_quote($id, '/') . '<\/id>.*?<\/user>/s';

    if (preg_match($patternUser, $addContent, $match)) {
        $userXml = $match[0];
        // user.txt に追記
        file_put_contents($userFile, $userXml . "\n", FILE_APPEND | LOCK_EX);

        // add.txt から削除
        $addContent = preg_replace($patternUser, '', $addContent);
        file_put_contents($addFile, trim($addContent));
    } else {
        echo "⚠ add.txt に該当ユーザーが見つかりません。";
        exit;
    }

    // --- sinsei.txt からも削除 ---
    $sinseiContent = file_exists($sinseiFile) ? file_get_contents($sinseiFile) : '';
    $patternSinsei = '/<sinsei>\s*<id>' . preg_quote($id, '/') . '<\/id>.*?<\/sinsei>/s';
    $sinseiContent = preg_replace($patternSinsei, '', $sinseiContent);
    file_put_contents($sinseiFile, trim($sinseiContent));

    echo "✅ ユーザー「{$id}」を承認し、user.txt に登録しました。";
}
?>
