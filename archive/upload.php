<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: text/plain; charset=utf-8");

$id = $_GET['id'] ?? '';
$pwd = $_GET['pwd'] ?? '';
$filename = $_GET['filename'] ?? '';

if ($id === '' || $pwd === '' || $filename === '') {
    echo "ERROR: missing parameters";
    exit;
}

// ログイン確認などの処理（省略）

// ファイル保存先ディレクトリ
$uploadDir = __DIR__ . "/dir/{$id}/";

// 日付をファイル名の先頭に追加
$date = date("Y-m-d");
$baseFilename = basename($filename);
$newFilename = "{$date}__{$baseFilename}";

// 保存するパス
$filePath = $uploadDir . $newFilename;

// ファイルを保存
if (move_uploaded_file($_FILES['file']['tmp_name'], $filePath)) {
    echo "ファイルアップロード成功: $newFilename";
} else {
    echo "ファイルアップロード失敗";
}


$id = $_GET['id'] ?? '';
$pwd = $_GET['pwd'] ?? '';
$filename = $_GET['filename'] ?? '';

if ($id === '' || $pwd === '' || $filename === '') {
    echo "ERROR: missing parameters";
    exit;
}

// ログイン確認などの処理（省略）

// ファイル保存先ディレクトリ
$uploadDir = __DIR__ . "/dir/{$id}/";

// 日付をファイル名の先頭に追加
$date = date("Y-m-d");
$baseFilename = basename($filename);
$newFilename = "{$date}__{$baseFilename}";

// 保存するパス
$filePath = $uploadDir . $newFilename;

// ファイルを保存
if (move_uploaded_file($_FILES['file']['tmp_name'], $filePath)) {
    echo "ファイルアップロード成功: $newFilename";
} else {
    echo "ファイルアップロード失敗";
}
?>

<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: text/plain; charset=utf-8");

// パラメータ取得
$id = $_GET['id'] ?? '';
$pwd = $_GET['pwd'] ?? '';

// ログイン確認（省略）

// ファイル保存先ディレクトリ
$uploadDir = __DIR__ . "/dir/{$id}/";

// ファイル一覧を取得
$files = glob($uploadDir . "*.csv"); // 必要に応じて拡張子を変更

// 日付でソート
usort($files, function($a, $b) {
    $dateA = substr(basename($a), 0, 10); // 日付部分を抽出
    $dateB = substr(basename($b), 0, 10); // 日付部分を抽出
    return strtotime($dateB) - strtotime($dateA); // 降順ソート
});

// 最新3つを残し、それ以前を削除
$filesToDelete = array_slice($files, 3); // 最新3つ以外を選択

foreach ($filesToDelete as $file) {
    // ファイル削除
    if (unlink($file)) {
        echo "削除成功: " . basename($file) . "\n";
    } else {
        echo "削除失敗: " . basename($file) . "\n";
    }
}

echo "古いファイルの削除処理が完了しました。";
?>
