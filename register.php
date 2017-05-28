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
if (isset($_POST['login']) and isset($_POST['password'])) {
    $login = mysqli_real_escape_string($db, $_POST['login']);
    $pass = mysqli_real_escape_string($db, $_POST['password']);


    $query = 'SELECT `password` FROM `users` WHERE `users`.`login` = "'.$login.'" LIMIT 1;';
    $result = mysqli_query($db, $query);
    $line = mysqli_fetch_array($result, MYSQLI_ASSOC);
    if (isset($line['password'])) {
        $error_msg = 'Пользователь с таким email уже зарегистрирован';
    } else {
        $query = 'INSERT INTO `users` (`login`, `password`) VALUES ("'.$login.'", "'.password_hash($pass, PASSWORD_DEFAULT).'");';
        $result = mysqli_query($db, $query);
        $query = 'SELECT `user_id` FROM `users` WHERE `users`.`login` = "'.$login.'" LIMIT 1;';
        $result = mysqli_query($db, $query);
        $line = mysqli_fetch_array($result, MYSQLI_ASSOC);
        echo mysqli_error();
        $_SESSION['user_id'] = $line['user_id'];
        $_SESSION['register_passed'] = True;
        mysqli_close($db);
        header("Location: create_account.php");
        exit;
    };
    mysqli_close($db);
};


 ?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>РЕГИСТРАЦИЯ</title>
    <link rel="icon" href="images/favicon.ico" type="image/x-icon">
    <link rel="shortcut icon" href="images/favicon.ico" type="image/x-icon">
    <link rel="stylesheet" href="css/style.css" media="screen" type="text/css" />
    <link href='http://fonts.googleapis.com/css?family=Open+Sans:400,700' rel='stylesheet' type='text/css'>
</head>

<body>
    <div id="login-form">
      <h1>РЕГИСТРАЦИЯ</h1>
        <fieldset>
            <form method="post">
                <input name="login" type="email" required placeholder="Email">
                <input name="password" type="password" required placeholder="Пароль" >
                <div><p class="error_msg"><?php echo $error_msg ?></p></div>
                <input type="submit" value="Зарегистрироваться">
                <div><a href="index.php">Вход</a></div>
            </form>
        </fieldset>


    </div> 
</body>
</html>