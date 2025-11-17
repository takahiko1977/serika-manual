<?php
//user.txt : <user><id>data1</id><pwd>data3</pwd><mail>data2</mail></user>
header("Cache-Control: no-cache, no-store, must-revalidate");
header("Pragma: no-cache");
header("Expires: 0");
//idがあるかどうかを返す
function isIdExist(string $id) {
    $text = file_get_contents("user/user.txt");
    if (!str_contains($text, "<user><id>".$id."</id><pwd>")) {
        return false;
    }
    return true;
}

//idとpasswordがあるかどうかを返す
function isIdAndPwdExist(string $id, string $pwd) {
    $text = file_get_contents("user/user.txt");
    if (!str_contains($text, "<user><id>".$id."</id><pwd>".$pwd."</pwd><mail>")) {
        return false;
    }
    return true;
}

//emailがあるか返す
function isEmailExist(string $mail) {
    $text = file_get_contents("user/user.txt");
    if (!str_contains($text, "</pwd><mail>".$mail."</mail></user>")) {
        return false;
    }
    return true;
}

//keyがあるか返す
function isKeyExist(string $key) {
    $text = file_get_contents("user/key.txt");
    if (!str_contains($text, "</pwd><key>".$key."</key></user>")) {
        return false;
    }
    return true;
}

function addUserData(string $id, string $pwd, string $mail, string $syoukai) {
    $userFile = __DIR__ . "/user/user.txt";
    $addFile = __DIR__ . "/user/add.txt";

    // 改行と空白除去
    $clean = fn($t) => preg_replace("/\s+/", "", $t);

    $contentUser = file_exists($userFile) ? $clean(file_get_contents($userFile)) : "";
    $contentAdd  = file_exists($addFile)  ? $clean(file_get_contents($addFile))  : "";

    $add = "<user><id>{$id}</id><pwd>{$pwd}</pwd><mail>{$mail}</mail><syoukai>{$syoukai}</syoukai></user>";
    $addClean = $clean($add);

    // 既に登録済みかどうかチェック
    if (strpos($contentUser, $addClean) !== false) {
        echo "IN_USER";
        return;
    }
    if (strpos($contentAdd, $addClean) !== false) {
        echo "IN_ADD";
        return;
    }

    // 仮登録ファイルに追記
    file_put_contents($addFile, $addClean . "\n", FILE_APPEND | LOCK_EX);
    echo "OK";
}

function addSyoukaiData(string $uname,string $name,string $siten,string $syurui,string $bank,string $mail) {
    $syoukaiFilePath     = __DIR__ . "/user/syoukailist.txt";
    $syoukaiFileDataPath = __DIR__ . "/user/syoukai.txt";

    // 改行と空白除去
    $clean = fn($v) => preg_replace("/\s+/", "", $v);

    // 既存ファイル内容を読み込み
    $syoukaiFile     = file_exists($syoukaiFilePath)     ? $clean(file_get_contents($syoukaiFilePath))     : "";
    $syoukaiFileData = file_exists($syoukaiFileDataPath) ? $clean(file_get_contents($syoukaiFileDataPath)) : "";

    // 保存データ構築
    $addData = "<puser><uname>{$uname}</uname><name>{$name}</name><siten>{$siten}</siten><syurui>{$syurui}</syurui><bank>{$bank}</bank><mail>{$mail}</mail></puser>";
    $addDataClean = $clean($addData);

    $add = "<uname>{$uname}</uname>";
    $addClean = $clean($add);

    // すでに存在チェック
    if (strpos($syoukaiFile, $addClean) !== false) {
        echo "IN_PUSER";
        return;
    }

    // 追記
    file_put_contents($syoukaiFilePath, $syoukaiFile . $addClean . "\n", FILE_APPEND | LOCK_EX);
    file_put_contents($syoukaiFileDataPath, $syoukaiFileData . $addDataClean . "\n", FILE_APPEND | LOCK_EX);

    echo "OK";
}

//新規Userを追記する
function addUserData2(string $id, string $pwd, string $mail, string $bank, string $name, string $syoukai) {
    $d1 = __DIR__ . "/user/user.txt"; // ← パスのスラッシュが抜けていた（修正）
    $d2 = __DIR__ . "/user/add.txt";
    $d3 = __DIR__ . "/user/bank.txt";
    
    $content = file_get_contents($d1);
    $contentNew = file_get_contents($d2);
    $contentBank = file_get_contents($d3);
    
    $add = "<user><id>{$id}</id><pwd>{$pwd}</pwd><mail>{$mail}</mail><syoukai>{$syoukai}</syoukai></user>";
    $bankData = "<user><bank>{$bank}</bank><name>{$name}</name></user>";

    // 改行削除関数を共通化（コード簡略化）
    $clean = fn($t) => str_replace(["\r\n", "\r", "\n"], "", $t);
    
    $newdata = $clean($content);
    $newdataNew = $clean($contentNew);
    $add = $clean($add);
    $bankData = $clean($bankData);

    // 厳密比較（=== false）を使う
    if (strpos($newdata, $add) !== false) {
        echo "IN";  // すでに user.txt に存在
    } elseif (strpos($newdataNew, $add) !== false) {
        echo "DB";  // すでに add.txt に存在
    } else {
        $newdataNew .= $add;
        $contentBank .= $bankData;
        file_put_contents($d2, $newdataNew);
        file_put_contents($d3, $contentBank);
        echo "OK";  // 新規追加
    }
}

//新規Userを追記する
function addUserData3(string $id, string $pwd, string $mail, string $bank, string $name, string $syoukai) {
    $d1 = __DIR__ . "/user/user.txt"; // ← パスのスラッシュが抜けていた（修正）
    $d2 = __DIR__ . "/user/add.txt";
    $d3 = __DIR__ . "/user/bank.txt";
    
    $content = file_get_contents($d1);
    $contentNew = file_get_contents($d2);
    $contentBank = file_get_contents($d3);
    
    $add = "<user><id>{$id}</id><pwd>{$pwd}</pwd><mail>{$mail}</mail><syoukai>{$syoukai}</syoukai></user>";
    $bankData = "<user><id>{$id}</id><bank>{$bank}</bank><name>{$name}</name></user>";

    // 改行削除関数を共通化（コード簡略化）
    $clean = fn($t) => str_replace(["\r\n", "\r", "\n"], "", $t);
    
    $newdata = $clean($content);
    $newdataNew = $clean($contentNew);
    $add = $clean($add);
    $bankData = $clean($bankData);

    // 厳密比較（=== false）を使う
    if (strpos($newdata, $add) !== false) {
        echo "IN";  // すでに user.txt に存在
    } elseif (strpos($newdataNew, $add) !== false) {
        echo "DB";  // すでに add.txt に存在
    } else {
        $newdataNew .= $add;
        $contentBank .= $bankData;
        file_put_contents($d2, $newdataNew);
        file_put_contents($d3, $contentBank);
        echo "OK";  // 新規追加
    }
}

//新規Keyを追記する
function addKeyData(string $filename, string $id, string $pwd, string $key) {
    $content = file_get_contents($filename);
    $newdata = $content."<user><id>".$id."</id><pwd>".$pwd."</pwd><key>".$key."</key></user>";
    $newdata = str_replace("\r\n", "", $newdata);
    $newdata = str_replace("\n", "", $newdata);
    $newdata = str_replace("\r", "", $newdata);
    file_put_contents($filename, $newdata);
}

//idを名前にしてフォルダを作りその中に固有データを1ファイル1データ種で保存する

//初回profile.php遷移時にid名のフォルダを作る
function makeProfileFolder(string $id) {
    $path = "profile/".$id;
    if (!is_dir($path)) {
        mkdir($path, 0777, true);
    }
}

//idからprofileを保存する
function setProfileProcess(string $id, string $name, string $sex, string $comment) {
    $pathName = "profile/".$id."/name.txt";
    $pathSex = "profile/".$id."/sex.txt";
    $pathComment = "profile/".$id."/comment.txt";
    file_put_contents($pathName, $name);
    file_put_contents($pathSex, $sex);
    file_put_contents($pathComment, $comment);    
}

//idからprofileの名前を返す
function getProfileName(string $id) {
    $path = "profile/".$id."/name.txt";
    if (file_exists($path)) {
        $content = file_get_contents($path);
        return $content;
    }
    return "";
}

//idからprofileの性別を返す
function getProfileSex(string $id) {
    $path = "profile/".$id."/sex.txt";
    if (file_exists($path)) {
        $content = file_get_contents($path);
        return $content;
    }
    return "";
}

//idからprofileの一言を返す
function getProfileComment(string $id) {
    $path = "profile/".$id."/comment.txt";
    if (file_exists($path)) {
        $content = file_get_contents($path);
        return $content;
    }
    return "";
}

//idからprofileの画像ファイル名を全て返す
function getProfileImage(string $id) {
    $path = "profile/".$id."/image.txt";
    if (file_exists($path)) {
        $content = file_get_contents($path);
        return "profile/".$id."/".$content;
    }
    return "";
}

//idからprofileのEmailを返す
function getProfileMail(string $id) {
    $text = file_get_contents("user/user.txt");
    $pos = strpos($text, "<user><id>".$id."</id><pwd>");
    $pos1 = strpos($text, "</pwd><mail>", $pos);
    $pos1 = $pos1 + 12;
    $pos2 = strpos($text, "</mail></user>", $pos1);
    $pos2 = $pos2 - $pos1;
    $dekita = substr($text, $pos1, $pos2);
    return $dekita;
}

function searchUsers(string $keyword): array {
    $text = file_get_contents("user/user.txt");
    $results = [];

    // ユーザー情報をパース
    preg_match_all('/<user><id>(.*?)<\/id><pwd>(.*?)<\/pwd><mail>(.*?)<\/mail><\/user>/', $text, $matches, PREG_SET_ORDER);

    foreach ($matches as $user) {
        $id = $user[1];
        $mail = $user[3];

        // 部分一致
        if (stripos($id, $keyword) !== false || stripos($mail, $keyword) !== false) {
            $results[] = [
                'id' => $id,
                'mail' => $mail,
            ];
        }
    }
    return $results;
}

function searchProfiles(string $keyword): array {
    $text = file_get_contents("user/user.txt");
    $results = [];

    preg_match_all(
        '/<user><id>(.*?)<\/id><pwd>(.*?)<\/pwd><mail>(.*?)<\/mail><name>(.*?)<\/name><comment>(.*?)<\/comment><sex>(.*?)<\/sex><\/user>/',
        $text,
        $matches,
        PREG_SET_ORDER
    );

    foreach ($matches as $user) {
        $id      = $user[1];
        $mail    = $user[3];
        $name    = $user[4];
        $comment = $user[5];
        $sex     = $user[6];

        // 検索対象をまとめて判定（部分一致）
        if (
            stripos($id, $keyword) !== false ||
            stripos($mail, $keyword) !== false ||
            stripos($name, $keyword) !== false ||
            stripos($comment, $keyword) !== false ||
            stripos($sex, $keyword) !== false
        ) {
            $results[] = [
                'id'      => $id,
                'mail'    => $mail,
                'name'    => $name,
                'comment' => $comment,
                'sex'     => $sex
            ];
        }
    }
    return $results;
}

