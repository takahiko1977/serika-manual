<?php
header("Cache-Control: no-cache, no-store, must-revalidate");
header("Pragma: no-cache");
header("Expires: 0");
session_start();
require_once 'user.php';

// login_new.php から来たデータを確認
if (!isset($_SESSION['new_user'])) {
    header("Location: login_new.php");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // 入力値を取得
    $bank = trim($_POST['bank']);
    $name = trim($_POST['name']);
    $syoukai = trim($_POST['syoukai']);

    // メール重複チェック
    $mail = $_SESSION['new_user']['mail'];
    if (!isEmailExist($mail)) {
        if (isset($_SESSION['new_user'])) {
            $id = $_SESSION['new_user']['id'];
            $pwd = $_SESSION['new_user']['pwd'];
            $mail = $_SESSION['new_user']['mail'];
            addUserData3($id,$pwd,$mail,$bank,$name,$syoukai);
        } else {
            echo "セッションが存在しません。";
        }
        header("Location: sinsei.php");
        exit;
    } else {
        echo "<script>alert('このメールアドレスはすでに登録されています。');</script>";
    }
}

$user = $_SESSION['new_user'];
?>

<!DOCTYPE html>
<html lang="ja">
<head>
<meta charset="UTF-8">
<title>口座情報の入力</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">

<div class="container mt-5">
    <div class="card shadow-sm">
        <div class="card-body">
            <h3 class="text-center mb-4">口座情報の入力</h3>

            <p>以下の情報を確認しました。</p>
            <ul>
                <li><strong>ID：</strong><?= htmlspecialchars($user['id']) ?></li>
                <li><strong>Email：</strong><?= htmlspecialchars($user['mail']) ?></li>
            </ul>
            
            <div class="container mt-5 mb-5">
                <div class="card shadow-sm">
                    <div class="card-body">
                        <h3 class="card-title text-center mb-4">振込案内</h3>

                        <p>以下の口座に利用料（または登録料）をお振込みください。</p>

                        <div class="alert alert-info">
                            <strong>■ 振込先情報</strong><br>
                            銀行名：横浜銀行<br>
                            支店名：厚木支店<br>
                            口座種別：普通預金 店番号(451)<br>
                            口座番号：1873030<br>
                            名義人：サイトウ タカヒコ<br>
                            金額：7,000円（税込）<br>
                        </div>

                        <hr>

                        <p>お振込後、以下のフォームに「あなたの口座番号」と「名義人名」を入力してください。</p>

                    </div>
                </div>
            </div>

            <form method="POST">
                <div class="mb-3">
                    <label for="syoukai" class="form-label">紹介者の選択</label>
                    <?php
                        $listFile = __DIR__ . "/user/syoukailist.txt";

                        if (file_exists($listFile)) {
                            $content = file_get_contents($listFile);

                            // <uname> ～ </uname> を全部取得
                            preg_match_all('/<uname>(.*?)<\/uname>/', $content, $matches);

                            $names = $matches[1];  // ← 名前だけの配列が入る
                        } else {
                            $names = [];
                        }
                    ?>
                    <select id="city" name="syoukai" class="form-select">
                        <?php foreach ($names as $u): ?>
                            <option value="<?= htmlspecialchars($u) ?>"><?= htmlspecialchars($u) ?></option>
                        <?php endforeach; ?>
                    </select>                
                </div>
                <div class="mb-3">
                    <label for="bank" class="form-label">口座番号の下3桁</label>
                    <input type="text" class="form-control" id="bank" name="bank" required>
                </div>
                <div class="mb-3">
                    <label for="name" class="form-label">名義人名</label>
                    <input type="text" class="form-control" id="name" name="name" required>
                </div>
                <div class="d-grid">
                    <button type="submit" class="btn btn-success">送信</button>
                </div>
            </form>
            <!-- 戻るボタン -->
            <div class="d-grid gap-2 mt-3">
              <a href="../index.php" class="btn btn-secondary">トップに戻る</a>
            </div>
        </div>
    </div>
</div>

</body>
</html>
