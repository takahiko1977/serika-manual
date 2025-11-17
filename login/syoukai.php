<?php
session_start();
require_once 'user.php';


if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // 入力値を取得
    $uname = trim($_POST['uname']);
    $bank = trim($_POST['bank']);
    $name = trim($_POST['name']);
    $mail = trim($_POST['mail']);
    $siten = trim($_POST['siten']);
    $syurui = trim($_POST['syurui']);

    addSyoukaiData($uname,$name,$siten,$syurui,$bank,$mail);
    header("Location: login.php");
    exit;
}

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
            <h3 class="text-center mb-4">紹介者情報の入力</h3>
            
            <div class="container mt-5 mb-5">
                <div class="card shadow-sm">
                    <div class="card-body">
                        <h3 class="card-title text-center mb-4">紹介料の振込説明</h3>

                        <div class="alert alert-info">
                            <strong>説明</strong><br>
                            serika.jarでの、開発に専念したいので<br>
                            開発者(齋藤 尚彦)は操作説明動画や、紹介動画<br>
                            上記を通して、販売促進をお願いしたいです。<br>
                            ですので、紹介システムを考案しました。<br>
                            <br>
                            紹介者として登録すると、ユーザーが購入の際に<br>
                            紹介者を選択でるリストに名前が入ります。<br>
                            営業経費として1購入につき、その紹介者には1000円支払われます。<br>
                            <br>
                            ※累計の営業経費累積が、50000円以上になりましたら振込を致します。<br>
                            及ばなかった場合は、次の決算日に加算します。
                            決算は偶数月の銀行が開いている月末日です。<br>
                            入力は間違いのないようにお願い致します。<br>
                            <br>

                        </div>

                        <hr>

                        <p>上記の内容をご確認後、以下のフォームに「あなたの口座番号等の情報」を入力してください。</p>

                    </div>
                </div>
            </div>

            <form method="POST">
                <div class="mb-3">
                    <label for="uname" class="form-label">お名前</label>
                    <input type="text" class="form-control" id="uname" name="uname" required>
                </div>
                <div class="mb-3">
                    <label for="name" class="form-label">口座名義名</label>
                    <input type="text" class="form-control" id="name" name="name" required>
                </div>
                <div class="mb-3">
                    <label for="syurui" class="form-label">口座の種類(普通等)</label>
                    <input type="text" class="form-control" id="syurui" name="syurui" required>
                </div>
                <div class="mb-3">
                    <label for="siten" class="form-label">支店名</label>
                    <input type="text" class="form-control" id="siten" name="siten" required>
                </div>
                <div class="mb-3">
                    <label for="bank" class="form-label">口座番号</label>
                    <input type="number" class="form-control" id="bank" name="bank" required>
                </div>
                <div class="mb-3">
                    <label for="mail" class="form-label">メールアドレス</label>
                    <input type="email" class="form-control" id="mail" name="mail" required>
                </div>
                <div class="d-grid">
                    <button type="submit" class="btn btn-success">送信</button>
                </div>
            </form>
        </div>
    </div>
</div>

</body>
</html>
