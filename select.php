<?php
session_start();
require_once 'funcs.php';
loginCheck();

//２．外来種報告SQL作成
$pdo = db_conn();
$stmt = $pdo->prepare('SELECT
                        contents.id as id, 
                        -- 上記下記のasは略記可能(よく省略されてある) 
                        contents.content as content,
                        users.name as name,
                        contents.image as image
                        -- 上記 image の中にパスが入っている
                        FROM 
                            contents
                        JOIN users
                        ON contents.user_id = users.id');
$status = $stmt->execute();

//３．報告履歴表示
$view = '';
if (!$status) {
    sql_error($stmt);
} else {
    while ($r = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $view .= '<div class="record"><p>';
        $view .= '<a href="detail.php?id=' . $r["id"] . '">';
        $view .= h($r['id']) . " " . h($r['content']) . '@' . h($r['name']);
        $view .= '</a>';
        $view .= "　";

        if ($_SESSION['kanri_flg'] === 1) {
            $view .= '<a class="btn btn-danger" href="delete.php?id=' . $r['id'] . '">';
            $view .= '削除';
            $view .= '</a>';
        }
        $view .= '<img src = "' . $r['image'] . '">';  //これ追加した
        $view .= '</p></div>';
    }
}
?>


<!DOCTYPE html>
<html lang="ja">

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>報告履歴</title>
    <!-- <link rel="stylesheet" href="css/login.css" /> -->
    <link rel="stylesheet" href="css/common.css" />
    <link rel="stylesheet" href="css/select.css" />

</head>

<body id="main">
    <!-- Head[Start] -->
    <header>
        <nav class="navbar navbar-default">
            <div class="container-fluid">
                <div class="navbar-header"><a class="navbar-brand" href="index.php">外来種報告</a></div>
                <div class="navbar-header"><a class="navbar-brand" href="logout.php">ログアウト</a></div>
                <div class="navbar-header user-name"><p><?= $_SESSION['user_name'] ?></p></div>
            </div>
        </nav>
    </header>
    <!-- Head[End] -->

    <!-- Main[Start] -->
    <div>
        <div class="container jumbotron"><?= $view ?></div>
    </div>
    <!-- Main[End] -->

</body>

</html>
