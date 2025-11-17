<?php
// === list.txt の絶対パス ===
$listFile = __DIR__ . "/apps/list.txt";

// list.txt が存在しない場合は空配列
if (!file_exists($listFile)) {
    $apps = [];
} else {
    // ファイル内容を取得
    $xmlContent = file_get_contents($listFile);

    // === 前処理 ===
    // UTF-8 BOM除去
    $xmlContent = preg_replace('/^\xEF\xBB\xBF/', '', $xmlContent);

    // 改行・タブ除去（タグ間は保つ）
    $xmlContent = preg_replace("/\r\n|\r|\n/", "", $xmlContent);

    // タグ間の空白を削除
    $xmlContent = preg_replace("/>\s+</", "><", $xmlContent);

    // <meta> タグを自己閉じに補正
    $xmlContent = preg_replace_callback(
        '/<meta\b([^>]*)>/i',
        function($m) {
            $attrs = trim($m[1]);
            if (substr($attrs, -1) === "/") {
                return "<meta $attrs>";
            }
            return "<meta $attrs />";
        },
        $xmlContent
    );

    // 制御文字の除去
    $xmlContent = preg_replace('/[\x00-\x1F\x7F]/u', '', $xmlContent);

    // ルートタグがない場合 <apps> でラップ
    if (strpos($xmlContent, "<apps>") === false) {
        $xmlContent = "<apps>" . $xmlContent . "</apps>";
    }

    // === XML パース ===
    libxml_use_internal_errors(true);
    $xml = simplexml_load_string($xmlContent);
    if ($xml === false) {
        $apps = [];
        $errorMessages = [];
        foreach (libxml_get_errors() as $error) {
            $errorMessages[] = trim($error->message);
        }
        libxml_clear_errors();
    } else {
        $apps = $xml->app;
    }
}
?>
<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <title>📦 Serika アプリリスト</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #f8f9fa;
        }
        .app-card {
            border-radius: 16px;
            overflow: hidden;
            margin-bottom: 1.5rem;
        }
        .app-title {
            font-size: 1.3rem;
            font-weight: 600;
        }
        .meta {
            font-size: 0.9rem;
            color: #555;
        }
        .app-desc {
            font-size: 0.95rem;
            color: #333;
        }
    </style>
</head>
<body>
<div class="container py-4">
    <h2 class="text-center mb-4">📦 Serika アプリリスト</h2>

    <?php if (isset($errorMessages) && count($errorMessages) > 0): ?>
        <div class="alert alert-danger">
            <strong>XMLパースエラー ⚠</strong><br>
            <ul>
                <?php foreach ($errorMessages as $err): ?>
                    <li><?= htmlspecialchars($err, ENT_QUOTES, 'UTF-8'); ?></li>
                <?php endforeach; ?>
            </ul>
        </div>
    <?php endif; ?>

    <?php if (empty($apps)): ?>
        <div class="alert alert-warning text-center">アプリ情報がまだ登録されていません。</div>
    <?php else: ?>
        <div class="row">
            <?php foreach ($apps as $app): ?>
                <div class="col-12 col-md-6 col-lg-4">
                    <div class="card shadow-sm app-card">
                        <div class="card-body">
                            <div class="app-title mb-2">
                                <?= htmlspecialchars($app->name ?? '(不明なアプリ)', ENT_QUOTES, 'UTF-8'); ?>
                            </div>

                            <?php if (isset($app->meta)): ?>
                                <div class="meta mb-2">
                                    <?php
                                    $cats = [];
                                    foreach (['category1','category2','category3'] as $c) {
                                        if (isset($app->meta[$c]) && trim($app->meta[$c]) !== '') {
                                            $cats[] = htmlspecialchars($app->meta[$c], ENT_QUOTES, 'UTF-8');
                                        }
                                    }
                                    echo implode(' / ', $cats);
                                    ?>
                                </div>
                            <?php endif; ?>

                            <p class="app-desc">
                                <?= nl2br(htmlspecialchars($app->data ?? '(説明なし)', ENT_QUOTES, 'UTF-8')); ?>
                            </p>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
    
    <div class="text-center">
        <a href="../index.php" class="btn btn-success">トップに戻る</a>
    </div>

</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
