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
    $idck = isIdExist($id);
    if ($idck && isIdAndPwdExist($id, $pwd)) {
        session_start();
        $_SESSION['user_id'] = $id;
        header("Location: profile.php");
        exit;
    }
}
?>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ログイン</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

    <style>
        body {
            background: linear-gradient(135deg, #6fb1fc 0%, #4364f7 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .card {
            border: none;
            border-radius: 1rem;
            box-shadow: 0 4px 20px rgba(0,0,0,0.15);
        }
        .btn-primary {
            background-color: #4364f7;
            border: none;
        }
        .btn-primary:hover {
            background-color: #3654d1;
        }
        .link-secondary {
            text-decoration: none;
        }
        .link-secondary:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>

<div class="container">
    <div class="row justify-content-center">
        <div class="col-11 col-sm-8 col-md-6 col-lg-4">
            <div class="card bg-white shadow-sm p-4">
                <div class="card-body">
                    <h3 class="card-title text-center mb-4">ログイン</h3>

                    <!-- ログインフォーム -->
                    <form method="POST">
                        <div class="mb-3">
                            <label for="id" class="form-label">ユーザー名</label>
                            <input type="text" class="form-control" id="id" name="id" placeholder="ユーザー名を入力" required>
                        </div>

                        <div class="mb-4">
                            <label for="pwd" class="form-label">パスワード</label>
                            <input type="password" class="form-control" id="pwd" name="pwd" placeholder="パスワードを入力" required>
                        </div>

                        <div class="d-grid mb-3">
                            <button type="submit" class="btn btn-primary btn-lg">ログイン</button>
                        </div>
                    </form>

                    <div class="text-center">
                        <a href="../index.php" class="btn btn-outline-primary w-100">トップに戻る</a>
                    </div>

                    <hr>

                    <!-- 新規登録ボタン -->
                    <div class="text-center">
                        <p class="mb-2">アカウントをお持ちでない方</p>
                        <a href="login_new.php" class="btn btn-outline-primary w-100">新規登録はこちら</a>
                    </div>

                </div>
            </div>
        </div>
    </div>
</div>

<!-- Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
