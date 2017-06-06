<?php 

	session_start();
    require_once 'connection.php';
    $db = mysqli_connect($host, $login, $password, $dbname);
    mysqli_set_charset($db, "utf8");


	if (!isset($_COOKIE['token'])) {
		echo('TOKEN NOT EXISTS');
	    header("Location: page.php");
	    exit;
	};

	if (!isset($_POST['new_status'])) {
		echo('STATUS NOT EXISTS');
		header("Location: page.php");
    	exit;
	};

	$query = 'SELECT `user_id` FROM `users` WHERE `users`.`token` = "'.$_COOKIE['token'].'" LIMIT 1;';
    $result = mysqli_query($db, $query);
    if ($result) {
    	$line = mysqli_fetch_array($result, MYSQLI_ASSOC);
    	$user_id = $line['user_id'];
    	echo('USER ID CATCHED');
    } else {
    	echo('USER ID NOT EXISTS');
    	header("Location: page.php");
	    exit;
    };

    $status = mysqli_real_escape_string($db, $_POST['new_status']);
    $query = 'UPDATE `accounts` SET `accounts`.`status` = "'.$status.'" WHERE `accounts`.`user_id`='.$user_id.';';
    $result = mysqli_query($db, $query) or die(mysqli_error($db));
    echo('ok');
    header("Location: page.php");
    exit;
    

?>