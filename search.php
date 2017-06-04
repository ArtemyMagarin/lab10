<?php 
	session_start();
	require_once 'connection.php';
	$db = mysqli_connect($host, $login, $password, $dbname);
	mysqli_set_charset($db, "utf8");

	$query = 'SELECT `user_id` FROM `users` WHERE `users`.`token` = "'.$_COOKIE['token'].'" LIMIT 1;';
	$result = mysqli_query($db, $query) or die(mysqli_error($db));
	if ($result) {
	    $line = mysqli_fetch_array($result, MYSQLI_ASSOC);
	    $user_id = $line['user_id'];
	};

	$line = mysqli_fetch_array(mysqli_query($db, 'SELECT `firstname`,`surname`, `city`, `bday` FROM `accounts` WHERE `accounts`.`user_id` = "'.$user_id.'" LIMIT 1;'));
	$name = $line['firstname']; 
	$surname = $line['surname'];
	$username =  $name." ".$surname;
	
	
	$IS_MY_PAGE = TRUE;


	$q = mysqli_real_escape_string($db, $_GET['q']);
	$keywords = preg_split("/[\s,]+/", $q);


	if (count($keywords) < 2) {
		if (count($keywords) > 0) {
			$keywords[] = $keywords[0];
		} else {
			$keywords[0] = "";
			$keywords[1] = "";
		};
	};


	$query = 'SELECT `user_id`,`firstname`,`surname`,`bday`,`city` FROM `accounts` WHERE 
	((`accounts`.`firstname` = "'.$keywords[0].'") and (`accounts`.`surname` = "'.$keywords[1].'")) or
	((`accounts`.`firstname` = "'.$keywords[1].'") and (`accounts`.`surname` = "'.$keywords[0].'")) or
	((`accounts`.`firstname` = "'.$keywords[0].'") or (`accounts`.`surname` = "'.$keywords[1].'")) or
	((`accounts`.`firstname` = "'.$keywords[1].'") or (`accounts`.`surname` = "'.$keywords[0].'")) or
	((`accounts`.`firstname` LIKE "%'.$keywords[0].'%") and (`accounts`.`surname` LIKE "%'.$keywords[1].'%")) or
	((`accounts`.`firstname` LIKE "%'.$keywords[1].'%") and (`accounts`.`surname` LIKE "%'.$keywords[0].'%")) or
	((`accounts`.`firstname` LIKE "%'.$keywords[0].'%") or (`accounts`.`surname` LIKE "%'.$keywords[1].'%")) or
	((`accounts`.`firstname` LIKE "%'.$keywords[1].'%") or (`accounts`.`surname` LIKE "%'.$keywords[0].'%")) limit 50;';
	$result = mysqli_query($db, $query);

	$people_list = array();

	while($line = mysqli_fetch_array($result))
	{
		$bday = "";
		$city = "";
		if ($line['bday']) {
			$bday = $line['bday'];
		};

		if ($line['city']) {
			$city = '<a>'.$line['city'].'</a>';
		};

		$people_list[] = ' <ul class="nav nav-pills"><li><a href="page.php?page_id='.$line['user_id'].'"><b>'.$line['firstname']." ".$line['surname'].'</b></a></li><li class="active pull-right"><a>'.$bday.'</a></li><li class="active pull-right">'.$city.'</li></ul>';
	};

	// foreach ($people_list as $key => $value) {
	// 	echo $value;
	// };
	
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
 	<title>Поиск "<?php echo $q ?>" </title>
    <link rel="icon" href="images/favicon.ico" type="image/x-icon">
    <link rel="shortcut icon" href="images/favicon.ico" type="image/x-icon">
    <link rel="stylesheet" type="text/css" href="css/bootstrap.min.css">
    <!-- <link rel="stylesheet" href="css/page.css" type="text/css" /> -->
    <link href='http://fonts.googleapis.com/css?family=Open+Sans:400,700' rel='stylesheet' type='text/css'>
</head>

<body>
<div class="container">
	<div class="row">
		<div class="navbar navbar-default">
			<div class="container">
				<div class="navbar-header">
					<a class="navbar-brand"><?php echo $username ?></a>
				</div>
				<div class="collapse navbar-collapse">
					<ul class="nav navbar-nav">
					<?php 
						if ($IS_MY_PAGE) {
							echo ('
								<li><a href="page.php">Моя страница</a></li>
								<li><a href="logout.php">Выход</a></li>
							');
						} else {

							$friend_status = 'Добавить в друзья';
							$query = 'SELECT `first`,`second`,`initiator` FROM `friends` WHERE (`friends`.`first` = '.$_GET['page_id'].' and `friends`.`second` = '.$_SESSION['user_id'].') or (`friends`.`first` = '.$_SESSION['user_id'].' and `friends`.`second` = '.$_GET['page_id'].');';
							$result = mysqli_query($db, $query);
							
							if ($result) {
								$line = mysqli_fetch_array($result ,MYSQLI_ASSOC);
								if ($line['initiator'] == $_SESSION['user_id']) {
									$friend_status = 'Отозвать заявку в друзья';
								};
								if ($line['initiator'] == $_GET['page_id']){
									$friend_status = 'Принять заявку в друзья';
								};
								if ($line['initiator'] == '0'){
									$friend_status = 'Убрать из друзей';
								};

							};


							echo ('
								<li><a href="add_to_friends.php?page_id='.$_GET['page_id'].'">'.$friend_status.'</a></li>
								<li><a href="page.php">Моя страница</a></li>
								<li><a href="logout.php">Выход</a></li>
							');
						};


					 ?>
						
					</ul>
					<form class="navbar-form navbar-right" method="get" action="search.php">
  							<div class="form-group">
						    	<input type="text" class="form-control navbar-right" placeholder="Поиск друзей" name="q">
						  	</div>
						  	<button type="submit" class="btn btn-default"><span class="glyphicon glyphicon-search"></span>
</button>
					</form>
				</div>
			</div>
		</div>
	</div>
</div>	

<div class="container">
	<div class="row">
		<div class="col-lg-1 col-md-1 col-sm-1 col-1">
			
		</div>
		<div class="col-lg-10 col-md-10 col-sm-10 col-10">
			<?php 
				foreach ($people_list as $key => $value) {
					echo '<div class="well">'.$value.'</div>';
				};

			 ?>

		</div>	
		<div class="col-lg-1 col-md-1 col-sm-1 col-1">

		</div>							
	</div>
</div>

</body>
</html>



