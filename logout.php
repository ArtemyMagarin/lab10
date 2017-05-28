<?php 
session_start();
echo $_SESSION['user_id'];
$db = mysqli_connect('a201168.mysql.mchost.ru', 'a201168_maga123', 'uAg51vl701', 'a201168_maga123');

mysqli_set_charset($db, "utf8");
mysqli_query($db, 'UPDATE `users` SET `token` = NULL WHERE `users`.`token` = "'.$_COOKIE['token'].'";');
setcookie('token',''); 
mysqli_close($db);
header("Location: index.php");
?>