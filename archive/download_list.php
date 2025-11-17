<?php
require_once __DIR__ . '/renew.php';
$file = __DIR__ . "/../login/apps/list.txt";
renewListTxt($file);

// ここから XML 出力
header("Content-Type: text/xml; charset=utf-8");
echo file_get_contents($file);

$id  = $_GET['id']  ?? '';
$pwd = $_GET['pwd'] ?? '';

if ($id === '' || $pwd === '') {
    echo '<?xml version="1.0" encoding="UTF-8"?><apps><error>missing id/pwd</error></apps>';
    exit;
}

// 認証チェック
$authUrl = "http://serika.cloudfree.jp/axwork/login/login_api.php?id=" 
          . urlencode($id) . "&pwd=" . urlencode($pwd);
$authResult = @file_get_contents($authUrl);

if (trim($authResult) !== "OK") {
    echo '<?xml version="1.0" encoding="UTF-8"?><apps><error>authentication failed</error></apps>';
    exit;
}

// list.txt 読み込み
$file = __DIR__ . "/../login/apps/list.txt";
if (!file_exists($file)) {
    echo '<?xml version="1.0" encoding="UTF-8"?><apps></apps>';
    exit;
}
$content = file_get_contents($file);

// 前処理
$content = str_replace("\r\n", "\n", $content);
$content = preg_replace('/^\xEF\xBB\xBF/', '', $content);
$content = trim($content);

// 重複 <apps> タグを除去
$content = preg_replace('/<\/apps>\s*<\/apps>/', '</apps>', $content);

// meta タグ修正
$content = preg_replace('/<meta([^>]*)(?<!\/)>/i', '<meta\1 />', $content);

// <app> ごとの整形
$apps = [];
preg_match_all('/<app>(.*?)<\/app>/s', $content, $matches);

foreach ($matches[1] as $block) {
    $block = trim($block);
    // 必須要素がなければ補完
    if (!preg_match('/<name>.*<\/name>/', $block)) {
        $block = "<name>unknown</name>\n" . $block;
    }
    if (!preg_match('/<meta\s*\/>/', $block)) {
        $block .= "\n<meta />";
    }
    if (!preg_match('/<data>.*<\/data>/s', $block)) {
        $block .= "\n<data></data>";
    }

    $apps[] = "    <app>\n" . $block . "\n    </app>";
}

// XML 出力
echo '<?xml version="1.0" encoding="UTF-8"?>' . "\n";
echo "<apps>\n" . implode("\n", $apps) . "\n</apps>\n";
