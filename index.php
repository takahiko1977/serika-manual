<?php
session_start();

// ユーザーがログインしている場合、名前を取得（例）
$userName = $_SESSION['user_id'] ?? 'ゲスト';
?>

<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <title>Serika トップメニュー</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #f8f9fa;
        }
        .menu-card {
            border-radius: 16px;
            overflow: hidden;
            transition: transform 0.2s ease;
            cursor: pointer;
        }
        .menu-card:hover {
            transform: translateY(-5px);
        }
        .menu-title {
            font-size: 1.2rem;
            font-weight: 600;
        }
        .menu-desc {
            font-size: 0.95rem;
            color: #555;
        }
        .welcome-text {
            font-size: 1.1rem;
            margin-bottom: 2rem;
        }
    </style>
</head>
<body>
<div class="container py-4">
    <div class="text-center mb-4">
        <h2>serika.jar</h2>
        <p class="welcome-text">ようこそ、<?= htmlspecialchars($userName, ENT_QUOTES, 'UTF-8'); ?> さん</p>
    </div>

    <div class="row g-4">
        <!-- プロフィール -->
        <div class="col-12 col-md-6 col-lg-4">
            <div class="card shadow-sm menu-card" onclick="location.href='login/profile.php';">
                <div class="card-body text-center">
                    <div class="menu-title mb-2">👤 プロフィール</div>
                    <div class="menu-desc">自分のプロフィールを確認・編集</div>
                </div>
            </div>
        </div>

        <!-- 全チャット -->
        <div class="col-12 col-md-6 col-lg-4">
            <div class="card shadow-sm menu-card" onclick="location.href='login/chat_global.php';">
                <div class="card-body text-center">
                    <div class="menu-title mb-2">💭 全チャ</div>
                    <div class="menu-desc">全体チャットを開始・閲覧</div>
                </div>
            </div>
        </div>

        <!-- チャット -->
        <div class="col-12 col-md-6 col-lg-4">
            <div class="card shadow-sm menu-card" onclick="location.href='login/chat_list.php';">
                <div class="card-body text-center">
                    <div class="menu-title mb-2">🧑‍🤝‍🧑 チャット</div>
                    <div class="menu-desc">ユーザーとのチャットを開始・閲覧</div>
                </div>
            </div>
        </div>

        <!-- その他機能例 -->
        <div class="col-12 col-md-6 col-lg-4">
            <div class="card shadow-sm menu-card" onclick="location.href='login/login.php';">
                <div class="card-body text-center">
                    <div class="menu-title mb-2">⚙️ ログイン</div>
                    <div class="menu-desc">サイトにログイン</div>
                </div>
            </div>
        </div>

        <div class="col-12 col-md-6 col-lg-4">
            <div class="card shadow-sm menu-card" onclick="location.href='manual/index.html';">
                <div class="card-body text-center">
                    <div class="menu-title mb-2">❓ ヘルプ</div>
                    <div class="menu-desc">操作方法の確認</div>
                </div>
            </div>
        </div>
        
        <!-- アプリリスト -->
        <div class="col-12 col-md-6 col-lg-4">
            <div class="card shadow-sm menu-card" onclick="location.href='login/show_list.php';">
                <div class="card-body text-center">
                    <div class="menu-title mb-2">📦 アプリリスト</div>
                    <div class="menu-desc">Serika アプリの一覧を確認・管理</div>
                </div>
            </div>
        </div>
        
        <!-- 振込先 -->
        <div class="col-12 col-md-6 col-lg-4">
            <div class="card shadow-sm menu-card" onclick="location.href='login/furikomi.html';">
                <div class="card-body text-center">
                    <div class="menu-title mb-2">🏧 振込先</div>
                    <div class="menu-desc">serika.jarアカウントの振込先</div>
                </div>
            </div>
        </div>
        
        <!-- 使用開始までの流れ -->
        <div class="col-12 col-md-6 col-lg-4">
            <div class="card shadow-sm menu-card" onclick="location.href='flow.html';">
                <div class="card-body text-center">
                    <div class="menu-title mb-2">⛩ 使用開始</div>
                    <div class="menu-desc">使用開始までの流れ</div>
                </div>
            </div>
        </div>

        <!-- 紹介者登録 -->
        <div class="col-12 col-md-6 col-lg-4">
            <div class="card shadow-sm menu-card" onclick="location.href='login/syoukai.php';">
                <div class="card-body text-center">
                    <div class="menu-title mb-2">🤝  紹介者登録</div>
                    <div class="menu-desc">紹介者として登録する</div>
                </div>
            </div>
        </div>


    </div>
    
    <!-- 既存のコンテナの後に追加 -->
    <div class="container py-4 text-center">
        <div class="position-relative mb-4">
            <!-- 横長画像 -->
            <img src="../icon/top.png" alt="serika banner" class="img-fluid rounded">
            <br>
            <!-- ダウンロードボタンを画像の上に重ねる -->
            <a href="downloads/serika.jar" class="btn btn-primary btn-lg position-absolute top-50 start-50 translate-middle">
                serika.jar ダウンロード
            </a>
        </div>
    </div>

</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
