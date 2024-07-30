<?php

session_start();
require_once 'funcs.php';
loginCheck();

//1. POSTつぶやき取得
$content = $_POST['content'];
$user_id = $_SESSION['user_id'];

// 画像アップロードの処理
$image = '';
if ( isset($_FILES['image']) ) {
    // フォームから画像が送られてきたら、、、以下を行う

    $upload_file = $_FILES['image']['tmp_name'];
    // 同じ人が同じファイル名を保存すると、上書きされちゃう。
    $extension = pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION);  //拡張子(extension)の取得
    $new_name = uniqid() . '.' . $extension;  // "一意の名.拡張子"

    //以下、保存先指定
    $image_path = 'img/' . $new_name;

    // 一時保存先から、生成したファイルの保存先に、移動

    if (move_uploaded_file($upload_file, $image_path)) { //第１引数から第２引数にuploadされたfileをmoveする
        // contentsテーブルに保存するために、ファイルパスを変数に入れる
        $image = $image_path;
    }  
}

// var_dump($_FILES);
// exit;  //こっから下の行は処理すんなよ、という変数

//2. DB接続します
$pdo = db_conn();

//３．つぶやき登録SQL作成
$stmt = $pdo->prepare('INSERT INTO contents(user_id, content, image, created_at)
                        VALUES(:user_id, :content, :image, NOW());');
$stmt->bindValue(':content', $content, PDO::PARAM_STR);
$stmt->bindValue(':user_id', $user_id, PDO::PARAM_INT);
$stmt->bindValue(':image', $image, PDO::PARAM_STR);
$status = $stmt->execute(); //実行

//４．つぶやき登録処理後
if (!$status) {
    sql_error($stmt);
} else {
    redirect('select.php');
}
