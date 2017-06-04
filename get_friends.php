<?php 
 
	require_once 'connection.php';
	$db = mysqli_connect($host, $login, $password, $dbname);
	mysqli_set_charset($db, "utf8");

	$initiator = 1;
	$friends_list = array();

	$query = 'SELECT `first`,`second`,`initiator` FROM `friends` WHERE (`friends`.`first` = '.$initiator.' or `friends`.`second` = '.$initiator.') and (`friends`.`initiator` = 0);';
	$result = mysqli_query($db, $query);

	while($line = mysqli_fetch_array($result))
	{
		if ($line['first'] == $initiator) {
			$row = mysqli_fetch_array(mysqli_query($db, 'SELECT `firstname`,`surname` FROM `accounts` WHERE `accounts`.`user_id` = "'.$line['second'].'" LIMIT 1;'));
			$friends_list[] = '<a href="page.php?page_id='.$line['second'].'">'.$row['firstname']." ".$row['surname'].'</a><br>';
		} else {
			$row = mysqli_fetch_array(mysqli_query($db, 'SELECT `firstname`,`surname` FROM `accounts` WHERE `accounts`.`user_id` = "'.$line['first'].'" LIMIT 1;'));
			$friends_list[] = '<a href="page.php?page_id='.$line['first'].'">'.$row['firstname']." ".$row['surname'].'</a><br>';
		}
	};

	
?>