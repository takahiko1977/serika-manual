<!-- search.php -->
<!DOCTYPE html>
<html lang="ja">
<head>
    <?php
        session_start();
        $selfId = $_SESSION['user_id'] ?? '1'; 

        if (isset($_POST['target_id'])) {
            $targetId = $_POST['target_id'];
            $roomName = uniqid("room_");

            $roomListFile = "chat/{$selfId}/roomlist.txt";
            if (!is_dir("chat/{$selfId}")) {
                mkdir("chat/{$selfId}", 0777, true);
            }

            $roomEntry = "<room>\n<name>{$roomName}</name>\n<user>{$targetId}</user>\n</room>\n";
            file_put_contents($roomListFile, $roomEntry, FILE_APPEND);

            $roomEntry2 = "<room>\n<name>{$roomName}</name>\n<user>{$selfId}</user>\n</room>\n";
            $roomListFile2 = "chat/{$targetId}/otherlist.txt";
            if (!is_dir("chat/{$targetId}")) {
                mkdir("chat/{$targetId}", 0777, true);
            }
            file_put_contents($roomListFile2, $roomEntry2, FILE_APPEND);

            $chatFile = "chat/{$selfId}/{$roomName}.txt";
            file_put_contents($chatFile, "=== チャット開始 ===\n");

            $message = "ルームを作成しました！（ルーム名: {$roomName}）";
        }
    ?>
    <meta charset="UTF-8">
    <title>ユーザー検索</title>
    <meta name="viewport" content="width=device-width, initial-scale=1"> <!-- スマホ対応 -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">

<div class="container mt-4">
    <h3 class="card-title mb-4 text-center">ルーム作成</h3>

    <?php if (!empty($message)) echo "<div class='alert alert-success'>$message</div>"; ?>

    <!-- 検索フォーム -->
    <form method="GET" class="mb-3">
        <div class="d-flex flex-column flex-sm-row gap-2">
            <input type="text" name="keyword" class="form-control" placeholder="IDやメールを入力">
            <button class="btn btn-primary w-100 w-sm-auto" type="submit">検索</button>
        </div>
    </form>

    <div class="mb-3">
        <?php
        require_once 'user.php';

        if (isset($_GET['keyword']) && $_GET['keyword'] !== '') {
            $keyword = $_GET['keyword'];
            $results = searchUsers($keyword);

            if (count($results) > 0) {
                echo "<ul class='list-group'>";
                foreach ($results as $user) {
                    echo "<li class='list-group-item'>";
                        echo "<form method='post' class='d-flex flex-column flex-sm-row align-items-sm-center gap-2 m-0'>";
                            echo "<span class='badge bg-secondary'>" 
                                . htmlspecialchars($user["id"], ENT_QUOTES, "UTF-8") 
                                . "</span>";
                            echo "<span class='flex-grow-1'>" 
                                . htmlspecialchars($user["mail"], ENT_QUOTES, "UTF-8") 
                                . "</span>";
                            echo "<input type='hidden' name='target_id' value='" 
                                . htmlspecialchars($user["id"], ENT_QUOTES, "UTF-8") . "'>";
                            echo "<button type='submit' class='btn btn-success w-100 w-sm-auto'>作成</button>";
                        echo "</form>";
                    echo "</li>";
                }
                echo "</ul>";
            } else {
                echo "<div class='alert alert-warning'>該当するユーザーはいません。</div>";
            }
        }
        ?>
    </div>

    <!-- ナビゲーションボタン -->
    <div class="d-grid gap-2">
        <a href="chat_list.php" class="btn btn-primary">チャットルーム一覧に戻る</a>
        <a href="../index.php" class="btn btn-secondary">トップに戻る</a>
    </div>

</div>

</body>
</html>
