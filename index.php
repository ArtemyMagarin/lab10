<?php 
session_start();
require_once 'connection.php';
$db = mysqli_connect($host, $login, $password, $dbname);
mysqli_set_charset($db, "utf8");

$error_msg = '';
if (isset($_COOKIE['token'])) {
    header("Location: page.php");
    exit;
};
if (isset($_POST['login']) and isset($_POST['password']) and ($_POST['login']!="") and ($_POST['password']!="")) {
    $login = mysqli_real_escape_string($db, $_POST['login']);
    $pass = mysqli_real_escape_string($db, $_POST['password']);




    $query = 'SELECT `user_id`, `password` FROM `users` WHERE `users`.`login` = "'.$login.'" LIMIT 1;';
    $result = mysqli_query($db, $query);
    if ($result) {
        $line = mysqli_fetch_array($result, MYSQLI_ASSOC);
    };

    if (isset($line['password'])) {
        if (!password_verify($pass, $line['password'])) {
            $error_msg = 'Проверьте правильность введенного пароля';
        } else {
            $_SESSION['user_id'] = $line['user_id'];
            $token = uniqid('', TRUE);
            mysqli_query($db, 'UPDATE `users` SET `token`="'.$token.'" WHERE `user_id`="'.$_SESSION['user_id'].'";');
            setcookie('token', $token, time()+(60*60*12));
            mysqli_close($db);
            header("Location: page.php");
            exit;
        };
    } else {
        $error_msg = 'Пользователь с таким email не зарегистрирован';
    };
    mysqli_close($db);
};

 ?>




<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>ВХОД</title>
    <link rel="icon" href="images/favicon.ico" type="image/x-icon">
    <link rel="shortcut icon" href="images/favicon.ico" type="image/x-icon">
    <link rel="stylesheet" href="css/style.css" media="screen" type="text/css" />
    <link href='http://fonts.googleapis.com/css?family=Open+Sans:400,700' rel='stylesheet' type='text/css'>
</head>

<body>

    <div id="login-form">
      <h1>АВТОРИЗАЦИЯ</h1>
        <fieldset>
            <form method="post">
                <input name="login" type="email" required placeholder="Email">
                <input name="password" type="password" required placeholder="Пароль" >
                <div><p class="error_msg"><?php echo $error_msg ?></p></div>
                <input type="submit" value="ВОЙТИ">
                <div><a href="register.php">Регистрация</a></div>
            </form>
        </fieldset>


    </div> 
</body>
</html>