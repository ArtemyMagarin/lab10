<?php 
session_start();
echo $_SESSION['user_id'];
require_once 'connection.php';
$db = mysqli_connect($host, $login, $password, $dbname);
mysqli_set_charset($db, "utf8");
mysqli_query($db, 'UPDATE `users` SET `token` = NULL WHERE `users`.`token` = "'.$_COOKIE['token'].'";');
setcookie('token',''); 
unset($_COOKIE['token']);
mysqli_close($db);
header("Location: index.php");
?>