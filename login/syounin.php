<?php
session_start();

// 🔐 管理パスワード
$admin_password = "serika_admin_2025";

// ログアウト処理
if (isset($_GET['logout'])) {
    unset($_SESSION['admin_login']);
    unset($_SESSION['approved_users']);
    header("Location: admin_confirm.php");
    exit;
}

// 管理者ログイン
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST["admin_pass"])) {
    if ($_POST["admin_pass"] === $admin_password) {
        $_SESSION['admin_login'] = true;
        header("Location: admin_confirm.php");
        exit;
    } else {
        $error = "パスワードが違います。";
    }
}

// 未ログインならログインフォーム表示
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
// データファイル
// ------------------------
$dir = __DIR__ . "/user/";
$userFile = $dir . "user.txt";
$addFile = $dir . "add.txt";
$sinseiFile = $dir . "sinsei.txt";

// ファイル読み込み
$sinseiData = file_get_contents($sinseiFile);
$addData = file_get_contents($addFile);

$sinseiData = str_replace(["\r\n", "\n", "\r"], "", $sinseiData);
$addData = str_replace(["\r\n", "\n", "\r"], "", $addData);

// 承認処理
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST["approve"])) {
    $id = $_POST["id"];
    $bank = $_POST["bank"];
    $name = $_POST["name"];

    $pattern = '/<sinsei><id>' . preg_quote($id, '/') . '<\/id><bank>' . preg_quote($bank, '/') . '<\/bank><name>' . preg_quote($name, '/') . '<\/name><\/sinsei>/s';
    if (preg_match($pattern, $sinseiData)) {
        $addPattern = '/<user><id>' . preg_quote($id, '/') . '<\/id><pwd>(.*?)<\/pwd><mail>(.*?)<\/mail><syoukai>(.*?)</syoukai><\/user>/s';
        if (preg_match($addPattern, $addData, $userMatches)) {
            $pwd = $userMatches[1];
            $mail = $userMatches[2];
            $syoukaisya = $userMatches[3];

            // user.txt に追加
            $userContent = file_get_contents($userFile);
            $newUser = "<user><id>{$id}</id><pwd>{$pwd}</pwd><mail>{$mail}</mail></user>";
            $userContent .= $newUser;
            file_put_contents($userFile, $userContent);

            // (紹介者名).txt に追加
            $syoukaisyaFile = __DIR__ . $syoukaisya .".txt";
            $syoukaisyaContent = file_get_contents($syoukaisyaFile);
            $newUser = "<user>{$id}</user>";
            $userContent .= $newUser;
            file_put_contents($syoukaisyaFile, $userContent);

            // add.txtとsinsei.txtから削除
            $addData = preg_replace($addPattern, '', $addData);
            file_put_contents($addFile, $addData);
            $sinseiData = preg_replace($pattern, '', $sinseiData);
            file_put_contents($sinseiFile, $sinseiData);

            // セッションに承認情報（銀行はマスクせず）
            $_SESSION['approved_users'][] = [
                'id' => $id,
                'pwd' => $pwd,
                'mail' => $mail,
                'bank' => $bank,
                'name' => $name
            ];

            $msg = "✅ ユーザー「{$id}」を承認しました。";
        } else {
            $msg = "❌ add.txtに対象ユーザーが見つかりません。";
        }
    } else {
        $msg = "❌ sinsei.txtで該当データが見つかりません。";
    }
}

// 完了ボタン → ログダウンロード（銀行下3桁マスク）
if (isset($_POST["download"])) {
    if (!empty($_SESSION['approved_users'])) {
        $timestamp = date("Y-m-d_H-i-s");
        $filename = "{$timestamp}_ログインデータログ.txt";
        $filepath = __DIR__ . "/" . $filename;

        $logContent = "";
        foreach ($_SESSION['approved_users'] as $u) {
            $bankMasked = substr($u['bank'], 0, -3) . '***'; // この時だけマスク
            $name = $u['name'] ?? '不明';
            $logContent .= "<user><id>{$u['id']}</id><pwd>{$u['pwd']}</pwd><mail>{$u['mail']}</mail><bank>{$bankMasked}</bank><name>{$name}</name></user>\n";
        }

        file_put_contents($filepath, $logContent);

        // ダウンロード
        header('Content-Type: text/plain');
        header("Content-Disposition: attachment; filename={$filename}");
        readfile($filepath);
        unlink($filepath);
        unset($_SESSION['approved_users']);
        exit;
    } else {
        $msg = "⚠ 承認済みデータがありません。";
    }
}

// sinsei一覧取得
preg_match_all('/<sinsei><id>(.*?)<\/id><bank>(.*?)<\/bank><name>(.*?)<\/name><\/sinsei>/', $sinseiData, $matches, PREG_SET_ORDER);
?>

<!DOCTYPE html>
<html lang="ja">
<head>
<meta charset="UTF-8">
<title>申請承認画面</title>
<style>
body { font-family: "Segoe UI", sans-serif; margin: 40px; }
table { border-collapse: collapse; width: 90%; margin-bottom: 30px; }
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
<th>銀行番号</th>
<th>名義人名</th>
<th>操作</th>
</tr>

<?php foreach ($matches as $m): ?>
<tr>
<td><?= htmlspecialchars($m[1]) ?></td>
<td><?= htmlspecialchars($m[2]) ?></td>
<td><?= htmlspecialchars($m[3]) ?></td>
<td>
    <form method="post">
        <input type="hidden" name="id" value="<?= htmlspecialchars($m[1]) ?>">
        <input type="hidden" name="bank" value="<?= htmlspecialchars($m[2]) ?>">
        <input type="hidden" name="name" value="<?= htmlspecialchars($m[3]) ?>">
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
