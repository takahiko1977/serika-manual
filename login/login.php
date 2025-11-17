<!-- login.php -->
<!DOCTYPE html>
<html lang="ja">
<head>
<?php
require_once 'user.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    loginProcess($_POST['id'], $_POST['pwd']);
}

function loginProcess($id, $pwd) {
    $filename = "user/user.txt";
    $idck = false;
    $idAndPwdck = false;
    $idck = isIdExist($id);
    if($idck){
        $idAndPwdck = isIdAndPwdExist($id, $pwd);
    }
    if($idAndPwdck&&$idck){
        session_start();
        $_SESSION['user_id'] = $id;
        header("Location: profile.php");
    }
}

?>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ログイン</title>
    <!-- Bootstrap CSS CDN -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">

<div class="container">
    <div class="row justify-content-center align-items-center vh-100">
        <div class="col-12 col-sm-8 col-md-6 col-lg-4">
            <div class="card shadow-sm">
                <div class="card-body">
                    <h3 class="card-title text-center mb-4">ログイン</h3>

                    <form method="POST">
                        <div class="mb-3">
                            <label for="username" class="form-label">ユーザー名</label>
                            <input type="text" class="form-control" id="username" name="id" placeholder="ユーザー名を入力" required>
                        </div>

                        <div class="mb-3">
                            <label for="password" class="form-label">パスワード</label>
                            <input type="password" class="form-control" id="password" name="pwd" placeholder="パスワードを入力" required>
                        </div>

                        <div class="d-grid mb-3">
                            <button type="submit" class="btn btn-primary">ログイン</button>
                        </div>

                    </form>

                    <!-- ボタン -->
                    <!-- <div class="d-grid gap-2">
                        <a href="profile.php" class="btn btn-secondary">ログインに戻る</a>
                    </div> -->

                    <div class="text-center">
                        <a href="../index.php" class="btn btn-success">トップに戻る</a>
                        <a href="login_new.php" class="btn btn-success">新規登録はこちら</a>
                    </div>

                </div>
            </div>
        </div>
    </div>
</div>

<!-- Bootstrap JS CDN -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>