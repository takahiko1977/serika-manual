<?php
$file = __DIR__ . '/../login/apps/list.txt';
if (!file_exists($file)) die("list.txt not found");

// ファイル読み込み
$content = file_get_contents($file);

// 先頭空白/改行削除
$content = trim($content);

// BOM除去
$content = preg_replace('/^\xEF\xBB\xBF/', '', $content);

// meta タグを自動で閉じる
$content = preg_replace('/<meta([^\/>]*)>/i', '<meta$1 />', $content);

// <apps> で囲む
if (!str_starts_with($content, '<apps>')) $content = "<apps>\n" . $content;
if (!str_ends_with($content, '</apps>')) $content .= "\n</apps>";

// simplexml で読み込み
libxml_use_internal_errors(true);
$xml = simplexml_load_string($content);
if ($xml === false) {
    echo "XML parsing failed\n";
    foreach(libxml_get_errors() as $err) {
        echo $err->message . "\n";
    }
    exit;
}

// DOM で整形して上書き保存
$dom = new DOMDocument('1.0','UTF-8');
$dom->preserveWhiteSpace = false;
$dom->formatOutput = true;
$dom->loadXML($xml->asXML());
file_put_contents($file, $dom->saveXML());

echo "Renew complete";
?>
