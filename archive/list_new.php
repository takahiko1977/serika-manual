<?php
$file = __DIR__ . '/../login/apps/list.txt';
if (!file_exists($file)) die("list.txt not found");

// ファイル読み込み
$content = file_get_contents($file);

// BOM・先頭空白削除
$content = preg_replace('/^\xEF\xBB\xBF|\s+/u', '', $content);

// 改行統一
$content = str_replace(["\r\n", "\r"], "\n", $content);

// <meta ...> を必ず閉じる
$content = preg_replace('/<meta\s*([^\/>]*)>/i', '<meta $1 />', $content);

// <apps>で囲む
if (!preg_match('/^<apps>/', $content)) $content = "<apps>\n" . $content;
if (!preg_match('/<\/apps>$/', $content)) $content .= "\n</apps>";

// SimpleXML で読み込み確認
libxml_use_internal_errors(true);
$xml = simplexml_load_string($content);
if ($xml === false) {
    foreach(libxml_get_errors() as $err){
        echo $err->message . "\n";
    }
    exit("XML parsing failed.\n");
}

// DOMDocument で整形して保存
$dom = new DOMDocument('1.0', 'UTF-8');
$dom->preserveWhiteSpace = false;
$dom->formatOutput = true;
$dom->loadXML($xml->asXML());
file_put_contents($file, $dom->saveXML());

echo "✅ XML修復完了";
?>
