<?php

session_start();
$lid = $_POST['lid'];
$lpw = $_POST['lpw'];

require_once 'funcs.php';
$pdo = db_conn();

// usersに、IDとWPがあるか確認する。
$stmt = $pdo->prepare('SELECT * FROM users WHERE lid = :lid;');
$stmt->bindValue(':lid', $lid, PDO::PARAM_STR);
$status = $stmt->execute();

//3. SQL実行時にエラーがある場合STOP
if($status === false) {
    sql_error($stmt);
}

//4. 抽出つぶやき数を取得
$val = $stmt->fetch();

if($val['id'] !== '') { //* PasswordがHash化の場合はこっちのIFを使う
    //Login成功時 該当レコードがあればSESSIONに値を代入
    $_SESSION['chk_ssid'] = session_id();
    $_SESSION['kanri_flg'] = $val['kanri_flg'];
    $_SESSION['user_name'] = $val['name'];
    //$val['id'] はuserのid.
    $_SESSION['user_id'] = $val['id'];
    header('Location: select.php');
} else {
    //Login失敗時(Logout経由)
    header('Location: login.php');
}
exit();
