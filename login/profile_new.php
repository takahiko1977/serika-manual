<!-- profile.php -->
<!DOCTYPE html>
<html lang="ja">
<head>
<?php
session_start();
require_once 'user.php';
$id = $_SESSION['user_id'];

$name = "";
$image = "";
$sex = "";
$comment = "";
$mail = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    global $name, $sex, $comment;
    $name = $_POST['name'];
    $sex = $_POST['sex'];
    $comment = $_POST['comment'];
    setProfileProcess($id, $name, $sex, $comment);
}

makeProfileFolder($id);

function getProfileProcess(string $id) {
    global $name, $image, $sex, $comment, $mail;
    $name = getProfileName($id);    
    $image = getProfileImage($id);    
    $sex = getProfileSex($id);    
    $comment = getProfileComment($id);    
    $mail = getProfileMail($id);    
}

if (isset($_SESSION['user_id'])) {
    getProfileProcess($id);
}

?>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>プロフィール</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">

<div class="container">
    <div class="row justify-content-center align-items-start mt-5">
        <div class="col-12 col-sm-8 col-md-6 col-lg-4">
            <div class="card shadow-sm">
                <div class="card-body text-center">
                    <h3 class="card-title mb-4">プロフィール</h3>

                        <!-- 写真 -->
                        <div class="mb-3">
                            <img src="<?php echo $image; ?>" alt="プロフィール写真" class="rounded-circle img-fluid" style="width:120px; height:120px;">
                        </div>

                        <form action="file_upload.php" method="POST" enctype="multipart/form-data" class="p-3 border rounded shadow-sm bg-white">
                            <div class="mb-3 text-center">
                                <label for="profile_image" class="form-label fw-bold">プロフィール画像</label>
                            </div>

                            <div class="d-flex gap-2 justify-content-center mb-3">
                                <!-- ファイル選択ボタン -->
                                <label class="btn btn-primary mb-0">
                                    ファイルを選択
                                    <input type="file" id="profile_image" name="profile_image" accept="image/*" hidden>
                                </label>

                                <!-- アップロードボタン -->
                                <button type="submit" class="btn btn-primary mb-0">アップロード</button>
                            </div>

                            <div class="form-text text-center">PNG, JPG, GIFなどの画像ファイルを選択してください</div>
                        </form>
                        <!-- <form action="file_upload.php" method="POST" enctype="multipart/form-data" class="p-3 border rounded shadow-sm bg-white">
                            <div class="mb-3 text-center">
                                <label for="profile_image" class="form-label fw-bold">プロフィール画像</label>
                                <input class="form-control" type="file" id="profile_image" name="profile_image" accept="image/*">
                                <div class="form-text">PNG, JPG, GIFなどの画像ファイルを選択してください</div>
                            </div>
                            <div class="d-grid">
                                <button type="submit" class="btn btn-primary">アップロード</button>
                            </div>
                        </form> -->

                    <form method="POST">

                        <!-- 名前 -->
                        <div class="mb-2 text-start">
                            <label class="form-label fw-bold">名前</label>
                            <input type="text" class="form-control" name="name" value="<?php echo htmlspecialchars($name, ENT_QUOTES, 'UTF-8'); ?>">
                        </div>

                        <!-- Email -->
                        <div class="mb-2 text-start">
                            <label class="form-label fw-bold">Email</label>
                            <p class="form-control-plaintext"><?php echo htmlspecialchars($mail, ENT_QUOTES, 'UTF-8'); ?></p>
                        </div>

                        <!-- 性別 -->
                        <div class="mb-3 text-start">
                            <label class="form-label fw-bold">性別</label>
                            <select id="sex" class="form-control" name="sex" aria-label="性別を選択">
                                <option value="" disabled selected>選択してください</option>
                                <option value="男性">男性</option>
                                <option value="女性">女性</option>
                            </select>
                        </div>

                        <!-- 一言 -->
                        <div class="mb-2 text-start">
                            <label class="form-label fw-bold">一言</label>
                            <textarea
                                name="comment"
                                id="comment"
                                class="form-control"
                                rows="5"
                                maxlength="500"
                                style="resize:vertical; overflow:auto;"
                                placeholder="ここに一言を書いてください..."><?php echo htmlspecialchars($comment, ENT_QUOTES, 'UTF-8'); ?></textarea>

                            <div class="form-text d-flex justify-content-between">
                                <small class="text-muted">改行可。ドラッグで高さ調整できます。</small>
                            </div>
                        </div>

                        <!-- ボタン -->
                        <div class="d-grid mb-3">
                            <button type="submit" class="btn btn-primary">更新する</button>
                        </div>
                    </form>

                    <div class="d-grid gap-2">
                        <!-- <a href="profile.php" class="btn btn-secondary">プロフィールに戻る</a> -->
                        <a href="../index.php" class="btn btn-secondary">ログインに戻る</a>
                        <a href="../index.php" class="btn btn-success">トップに戻る</a>
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
