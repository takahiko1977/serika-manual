<?php
session_start();

// 🔐 管理パスワード
$admin_password = "serika_admin_2025";

// ------------------------
// ログアウト処理
// ------------------------
if (isset($_GET['logout'])) {
    unset($_SESSION['admin_login']);
    unset($_SESSION['approved_users']);
    header("Location: admin_confirm.php");
    exit;
}

// ------------------------
// 管理者ログイン処理
// ------------------------
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST["admin_pass"])) {
    if ($_POST["admin_pass"] === $admin_password) {
        $_SESSION['admin_login'] = true;
        header("Location: admin_confirm.php");
        exit;
    } else {
        $error = "パスワードが違います。";
    }
}

// 未ログインならログイン画面
if (empty($_SESSION['admin_login'])):
?>
<!DOCTYPE html>
<html lang="ja">
<head>
<meta charset="UTF-8">
<title>管理者ログイン</title>
<style>
body { font-family: "Segoe UI", sans-serif; background: #f9f9f9; display: flex; justify-content: center; align-items: center; height: 100vh; }
form { background: #fff; padding: 30px; border-radius: 12px; box-shadow: 0 0 10px rgba(0,0,0,0.1); }
input[type=password] { width: 100%; padding: 10px; margin-top: 10px; border-radius: 8px; border: 1px solid #ccc; }
button { margin-top: 15px; padding: 8px 16px; border-radius: 6px; border: none; background: #007bff; color: white; cursor: pointer; }
button:hover { background: #0069d9; }
p.error { color: red; }
</style>
</head>
<body>
<form method="post">
  <h2>🔒 管理者ログイン</h2>
  <p>パスワードを入力してください。</p>
  <?php if (!empty($error)) echo "<p class='error'>$error</p>"; ?>
  <input type="password" name="admin_pass" placeholder="管理パスワード" required>
  <button type="submit">ログイン</button>
</form>
</body>
</html>
<?php
exit;
endif;

// ------------------------
// データファイル読み込み
// ------------------------
$dir = __DIR__ . "/user/";
$addData = file_get_contents($dir . "add.txt");
$bankData = file_get_contents($dir . "bank.txt");

// 改行除去
$addData = str_replace(["\r", "\n"], "", $addData);
$bankData = str_replace(["\r", "\n"], "", $bankData);

// ------------------------
// XML 解析（add.txt）
// ------------------------
preg_match_all(
    '/<user><id>(.*?)<\/id><pwd>(.*?)<\/pwd><mail>(.*?)<\/mail><syoukai>(.*?)<\/syoukai><\/user>/',
    $addData,
    $addMatches,
    PREG_SET_ORDER
);

// ------------------------
// XML 解析（bank.txt）
// ------------------------
preg_match_all(
    '/<user><id>(.*?)<\/id><bank>(.*?)<\/bank><name>(.*?)<\/name><\/user>/',
    $bankData,
    $bankMatches,
    PREG_SET_ORDER
);

// ------------------------
// ID をキーにしてマージ
// ------------------------
$users = [];

foreach ($addMatches as $u) {
    $id = $u[1];
    $users[$id] = [
        'id'      => $id,
        'pwd'     => $u[2],
        'mail'    => $u[3],
        'syoukai' => $u[4],
        'bank'    => '',
        'name'    => ''
    ];
}

foreach ($bankMatches as $b) {
    $id = $b[1];
    if (!isset($users[$id])) continue; // add.txt に無いものは無視
    $users[$id]['bank'] = $b[2];
    $users[$id]['name'] = $b[3];
}

// ------------------------
// 承認ボタン処理
// ------------------------
if (isset($_POST['approve'])) {
    $id      = $_POST['id'];
    $mail    = $_POST['mail'];
    $syoukai = $_POST['syoukai'];
    $bank    = $_POST['bank'];
    $name    = $_POST['name'];

    // ----------------------------------
    // add.txt からユーザー情報（pwd含む）を抽出（削除前！）
    // ----------------------------------
    $addFile = $dir . "add.txt";
    $addRaw  = file_get_contents($addFile);

    $patternUser = '/<user><id>' . preg_quote($id, '/') . '<\/id><pwd>(.*?)<\/pwd><mail>(.*?)<\/mail><syoukai>(.*?)<\/syoukai><\/user>/';

    if (preg_match($patternUser, $addRaw, $mm)) {
        $pwd     = $mm[1];
        $email   = $mm[2];
        // syoukai は POST で来てるので mm[3] は使わない
    } else {
        $pwd   = "";
        $email = $mail;
    }

    // ----------------------------------
    // ① add.txtから該当ユーザー削除
    // ----------------------------------
    $addNew = preg_replace($patternUser, '', $addRaw);
    file_put_contents($addFile, $addNew, LOCK_EX);

    // ----------------------------------
    // ② bank.txtから該当ユーザー削除
    // ----------------------------------
    $bankFile = $dir . "bank.txt";
    $bankRaw  = file_get_contents($bankFile);

    $patternBank = '/<user><id>' . preg_quote($id, '/') . '<\/id>.*?<\/user>/';
    $bankNew = preg_replace($patternBank, '', $bankRaw);

    file_put_contents($bankFile, $bankNew, LOCK_EX);

    // ----------------------------------
    // ③ user/紹介者.txt に <user>id</user> を追記
    // ----------------------------------
    $syoukaiFile = $dir . $syoukai . ".txt";
    $line = "<user>{$id}</user>\n";

    file_put_contents($syoukaiFile, $line, FILE_APPEND | LOCK_EX);

    // ----------------------------------
    // ④ user/user.txt に本登録として 1 回だけ追記
    // ----------------------------------
    $userFile = $dir . "user.txt";

    // 重複書き込み防止
    $existing = file_get_contents($userFile);
    if (strpos($existing, "<id>{$id}</id>") === false) {

        $newUser =
            "<user><id>{$id}</id><pwd>{$pwd}</pwd><mail>{$email}</mail></user>\n";

        file_put_contents($userFile, $newUser, FILE_APPEND | LOCK_EX);
    }

    // ----------------------------------
    // セッションへ
    // ----------------------------------
    $_SESSION['approved_users'][] = [
        "id"      => $id,
        "mail"    => $email,
        "syoukai" => $syoukai,
        "bank"    => $bank,
        "name"    => $name
    ];

    $msg = "ID: {$id} を承認しました。";
}

// ------------------------
// ダウンロード（ログ生成）
// ------------------------
if (isset($_POST['download'])) {
    $log = "承認ログ\n\n";

    foreach ($_SESSION['approved_users'] as $u) {
        $log .= "ID: {$u['id']}\n";
        $log .= "Mail: {$u['mail']}\n";
        $log .= "紹介者: {$u['syoukai']}\n";
        $log .= "銀行番号: {$u['bank']}\n";
        $log .= "名義: {$u['name']}\n";
        $log .= "-------------------------\n";
    }

    header('Content-Type: text/plain');
    header('Content-Disposition: attachment; filename="log.txt"');
    echo $log;
    exit;
}

?>
<!DOCTYPE html>
<html lang="ja">
<head>
<meta charset="UTF-8">
<title>申請承認画面</title>
<style>
body { font-family: "Segoe UI", sans-serif; margin: 40px; }
table { border-collapse: collapse; width: 95%; margin-bottom: 30px; }
th, td { border: 1px solid #aaa; padding: 8px; text-align: center; }
th { background: #f0f0f0; }
button { padding: 6px 12px; border-radius: 6px; border: none; cursor: pointer; }
button.approve { background: #4CAF50; color: white; }
button.approve:hover { background: #45a049; }
button.download { background: #007bff; color: white; padding: 10px 18px; }
button.download:hover { background: #0056b3; }
a.logout { float: right; text-decoration: none; color: #333; background: #eee; padding: 4px 8px; border-radius: 4px; }
a.logout:hover { background: #ddd; }
.message { color: green; font-weight: bold; margin-bottom: 10px; }
</style>
</head>
<body>

<a href="?logout=1" class="logout">🚪 ログアウト</a>
<h2>💼 申請承認リスト</h2>

<?php if (!empty($msg)) echo "<p class='message'>{$msg}</p>"; ?>

<table>
<tr>
    <th>ID</th>
    <th>メール</th>
    <th>紹介者</th>
    <th>銀行番号</th>
    <th>名義</th>
    <th>操作</th>
</tr>

<?php foreach ($users as $u): ?>
<tr>
    <td><?= htmlspecialchars($u['id']) ?></td>
    <td><?= htmlspecialchars($u['mail']) ?></td>
    <td><?= htmlspecialchars($u['syoukai']) ?></td>
    <td><?= htmlspecialchars($u['bank']) ?></td>
    <td><?= htmlspecialchars($u['name']) ?></td>
    <td>
        <form method="post">
            <input type="hidden" name="id" value="<?= htmlspecialchars($u['id']) ?>">
            <input type="hidden" name="mail" value="<?= htmlspecialchars($u['mail']) ?>">
            <input type="hidden" name="syoukai" value="<?= htmlspecialchars($u['syoukai']) ?>">
            <input type="hidden" name="bank" value="<?= htmlspecialchars($u['bank']) ?>">
            <input type="hidden" name="name" value="<?= htmlspecialchars($u['name']) ?>">
            <button type="submit" name="approve" class="approve">承認</button>
        </form>
    </td>
</tr>
<?php endforeach; ?>

</table>

<?php if (!empty($_SESSION['approved_users'])): ?>
<form method="post">
    <button type="submit" name="download" class="download">💾 完了（ログをダウンロード）</button>
</form>
<?php endif; ?>

</body>
</html>
