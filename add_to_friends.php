<?php 
	ini_set('display_errors', '1');
	require_once 'connection.php';
	$db = mysqli_connect($host, $login, $password, $dbname);
	mysqli_set_charset($db, "utf8");

	if (!isset($_GET['page_id']) or !isset($_COOKIE['token'])) {
		header("Location: page.php?page_id=".$_GET['page_id']);
        exit;
	} else {


	$query = 'SELECT `user_id` FROM `users` WHERE `users`.`token` = "'.$_COOKIE['token'].'" LIMIT 1;';
	$result = mysqli_query($db, $query) or die(mysqli_error($db));
	if ($result) {
	    $line = mysqli_fetch_array($result, MYSQLI_ASSOC);
	    $user_id = $line['user_id'];
	} else {
		header("Location: page.php?page_id=".$_GET['page_id']);
        exit;
	};
	// echo($user_id);

	$friend_status = 'Добавить в друзья';
	$query = 'SELECT `first`,`second`,`initiator` FROM `friends` WHERE (`friends`.`first` = '.$_GET['page_id'].' and `friends`.`second` = '.$user_id.') or (`friends`.`first` = '.$user_id.' and `friends`.`second` = '.$_GET['page_id'].');';
	$result = mysqli_query($db, $query) or die(mysqli_error($db));

	if ($result) {
		$line = mysqli_fetch_array($result ,MYSQLI_ASSOC);
		if ($line['initiator'] == $user_id) {
			$friend_status = 'Отозвать заявку в друзья';
			echo $friend_status;
			mysqli_query($db, 'DELETE FROM `friends` WHERE (`friends`.`first` = "'.$_GET['page_id'].'" and `friends`.`second` = "'.$user_id.'") or (`friends`.`first` = "'.$user_id.'" and `friends`.`second` = "'.$_GET['page_id'].'");') or die(mysqli_error($db));
			header("Location: page.php?page_id=".$_GET['page_id']);
	        exit;
		};
		if ($line['initiator'] == $_GET['page_id']){
			$friend_status = 'Принять заявку в друзья';
			echo $friend_status;
			mysqli_query($db, 'UPDATE `friends` SET `initiator`="0" WHERE  (`friends`.`first` = "'.$_GET['page_id'].'" and `friends`.`second` = "'.$user_id.'") or (`friends`.`first` = "'.$user_id.'" and `friends`.`second` = "'.$_GET['page_id'].'");') or die(mysqli_error($db));
			header("Location: page.php?page_id=".$_GET['page_id']);
	        exit;
		};
		if ($line['initiator'] == '0'){
			$friend_status = 'Убрать из друзей';
			echo $friend_status;
			mysqli_query($db, 'UPDATE `friends` SET `initiator`="'.$_GET['page_id'].'" WHERE (`friends`.`first` = '.$_GET['page_id'].' and `friends`.`second` = '.$user_id.') or (`friends`.`first` = '.$user_id.' and `friends`.`second` = '.$_GET['page_id'].');') or die(mysqli_error($db));
			header("Location: page.php?page_id=".$_GET['page_id']);
	        exit;
		};

	}; 
		echo "else";
		mysqli_query($db, 'INSERT INTO `friends` (`first`, `second`, `initiator`) VALUES ("'.$user_id.'","'.$_GET['page_id'].'","'.$user_id.'");');
		header("Location: page.php?page_id=".$_GET['page_id']) or die(mysqli_error($db));
        exit;
	
};

	// header("Location: page.php?page_id=".$_GET['page_id']);
 //    exit;

?>