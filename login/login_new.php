<!-- login_new.php -->
<!DOCTYPE html>
<html lang="ja">
<head>
<?php
session_start(); // ★ セッションを開始
require_once 'user.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // 入力値を取得
    $id = trim($_POST['id']);
    $pwd = trim($_POST['pwd']);
    $mail = trim($_POST['mail']);

    // メール重複チェック
    if (!isEmailExist($mail)) {
        // ★ セッションに一時保存
        $_SESSION['new_user'] = [
            'id' => $id,
            'pwd' => $pwd,
            'mail' => $mail
        ];

        // ★ sinsei.php へ遷移
        header("Location: sinsei.php");
        exit;
    } else {
        echo "<script>alert('このメールアドレスはすでに登録されています。');</script>";
    }
}
?>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>新規申請</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">

<div class="container">
    <div class="row justify-content-center align-items-center vh-100">
        <div class="col-12 col-sm-8 col-md-6 col-lg-4">
            <div class="card shadow-sm">
                <div class="card-body">
                    <h3 class="card-title text-center mb-4">新規申請</h3>

                    <form method="POST">
                        <div class="mb-3">
                            <label for="username" class="form-label">ユーザー名</label>
                            <input type="text" class="form-control" id="username" name="id" placeholder="ユーザー名を入力" required>
                        </div>

                        <div class="mb-3">
                            <label for="password" class="form-label">パスワード</label>
                            <input type="password" class="form-control" id="password" name="pwd" placeholder="パスワードを入力" required>
                        </div>

                        <div class="mb-3">
                            <label for="mail" class="form-label">Email</label>
                            <input type="email" class="form-control" id="mail" name="mail" placeholder="メールアドレスを入力" required>
                        </div>

                        <div class="d-grid mb-3">
                            <button type="submit" class="btn btn-primary">申請</button>
                        </div>
                    </form>

                    <div class="d-grid gap-2">
                        <a href="../index.php" class="btn btn-secondary">ログインに戻る</a>
                        <a href="../index.php" class="btn btn-success">トップに戻る</a>
                    </div>

                </div>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
