<!DOCTYPE html>
<html lang="ja">
<head>
<meta charset="UTF-8">
<title>ファイル共有アップロード</title>
<style>
body { font-family: sans-serif; background: #f8f8f8; padding: 20px; }
form { margin-bottom: 20px; padding: 10px; background: #fff; border-radius: 8px; width: 300px; }
label { display: block; margin-bottom: 8px; }
button { margin-top: 10px; padding: 6px 12px; }
h2, h3 { margin-top: 0; }
</style>
</head>
<body>
<h2>ファイル共有アップロード</h2>

<form action="upload.php" method="GET" enctype="multipart/form-data">
  <label>ID: <input type="text" name="id" required></label>
  <label>Password: <input type="password" name="pwd" required></label>
  <input type="file" name="file" required>
  <button type="submit">アップロード</button>
</form>

<hr>

<h3>ファイル一覧を見る</h3>
<form action="files.php" method="GET">
  <label>ID: <input type="text" name="id" required></label>
  <label>Password: <input type="password" name="pwd" required></label>
  <button type="submit">一覧表示</button>
</form>

</body>
</html>
