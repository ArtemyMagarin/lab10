<?php
 ini_set('display_errors', E_ALL);
session_start();
require_once 'connection.php';
$db = mysqli_connect($host, $login, $password, $dbname);
mysqli_set_charset($db, "utf8");

if (!isset($_COOKIE['token'])) {
    header("Location: index.php");
    exit;
};

if (isset($_GET['page_id'])) {
	$result =  mysqli_query($db, 'select * from `users` where `users`.`user_id` = '.$_GET['page_id'].';') or die(mysqli_error($db));
	$line = mysqli_fetch_array($result);
	if ($line) {

	} else {
		header("Location: page.php");
	    exit;
	};
	
    
};

// if registration isn't completed or no login
if (isset($_COOKIE['token']) and $_COOKIE['token']!="") {
	$query = 'SELECT `user_id`, `password` FROM `users` WHERE `users`.`token` = "'.$_COOKIE['token'].'" LIMIT 1;';
	    $result = mysqli_query($db, $query);

	    if ($result) {
	        $line = mysqli_fetch_array($result, MYSQLI_ASSOC);
	$query = 'SELECT `user_id`,`firstname`,`surname`,`bday`,`sex`,`status`,`marital_status`,`city`,`job`,`edu`,`interests` FROM `accounts` WHERE `accounts`.`user_id` = "'.$line['user_id'].'" LIMIT 1;';
	        $result = mysqli_query($db, $query);
	        $line = mysqli_fetch_array($result, MYSQLI_ASSOC);
	        if (!$line) {
	            mysqli_close($db);
	            header("Location: create_account.php");
	            exit;
	        }

	        
			foreach ($line as $key => $value) {
				if (gettype($value) == 'NULL') {
					$line[$key] = "не указано";
				};
			};
	        $_SESSION['user_id'] = $line['user_id'];
	        $user_id = $_SESSION['user_id'];
	        $name = $line['firstname']; 
			$surname = $line['surname'];
			$bday = $line['bday'];
			$gender = $line['sex'];
			if ($gender == 'male') {
				$gender = 'мужской';
			};
			if ($gender == 'female') {
				$gender = 'женский';
			};
			if ($gender == 'other') {
				$gender = 'другой';
			};
			$status = $line['status'];
			$marital_status = $line['marital_status'];
			$city = $line['city'];
			$job = $line['job'];
			$edu = $line['edu'];
			$interests = $line['interests'];
			$username =  $name." ".$surname;

	       
	    };
};



if ((!isset($_GET['page_id']) or ($_GET['page_id'] == $_SESSION['user_id']))) {
	$IS_MY_PAGE = TRUE;
	$_GET['page_id'] = $_SESSION['user_id'];
} else {
	$IS_MY_PAGE = FALSE;
	$query = 'SELECT `firstname`,`surname`,`bday`,`sex`,`status`,`marital_status`,`city`,`job`,`edu`,`interests` FROM `accounts` WHERE `accounts`.`user_id` = "'.$_GET['page_id'].'" LIMIT 1;';
	$result = mysqli_query($db, $query);
	if ($result) {
		$line = mysqli_fetch_array($result, MYSQLI_ASSOC);

		foreach ($line as $key => $value) {
			if (gettype($value) == 'NULL') {
				$line[$key] = "не указано";
			};
		};
		$name = $line['firstname']; 
		$surname = $line['surname'];
		$bday = $line['bday'];
		$gender = $line['sex'];
		if ($gender == 'male') {
			$gender = 'мужской';
		};
		if ($gender == 'female') {
			$gender = 'женский';
		};
		if ($gender == 'other') {
			$gender = 'другой';
		};
		$status = $line['status'];
		$marital_status = $line['marital_status'];
		$city = $line['city'];
		$job = $line['job'];
		$edu = $line['edu'];
		$interests = $line['interests'];
		$username =  $name." ".$surname;
	};
};



 ?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title><?php echo $username ?></title>
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
								<li><a href="edit_page.php">Редактировать</a></li>
								<li><a href="logout.php">Выход</a></li>
							');
						} else {

							$friend_status = 'Добавить в друзья';
							$query = 'SELECT `first`,`second`,`initiator` FROM `friends` WHERE (`friends`.`first` = '.$_GET['page_id'].' and `friends`.`second` = '.$_SESSION['user_id'].') or (`friends`.`first` = '.$_SESSION['user_id'].' and `friends`.`second` = '.$_GET['page_id'].');';
							$result = mysqli_query($db, $query);
							
							if ($line = mysqli_fetch_array($result ,MYSQLI_ASSOC)) {
								
								if ($line['initiator'] == $_SESSION['user_id']) {
									$friend_status = 'Отозвать заявку в друзья';
								};
								if ($line['initiator'] == $_GET['page_id']){
									$friend_status = 'Принять заявку в друзья';
								};
								if ($line['initiator'] == '0'){
									$friend_status = 'Убрать из друзей';
								};

							} else {
								// header("Location: page.php");
							 //    exit;
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
		<div class="col-lg-4 col-md-4 col-sm-4 col-4">
		<?php 
			if ($IS_MY_PAGE) {
				echo 'Cтатус: <span id="status">'.$status.'</span> <a style="cursor: pointer;" onclick="changeStatus(document.getElementById(\'status\')); this.style = \'display:none\'"">Изменить</a><br>';
			} else {
				echo 'Cтатус: '. $status.'<br>';
			};
		 ?>
			
			Пол: <?php echo $gender; ?> <br>
			Дата рождения: <?php echo $bday ?> <br>
			Cемейное положение: <?php echo $marital_status; ?><br> 

		</div>
		<div class="col-lg-4 col-md-4 col-sm-4 col-4">
			Город:  <?php echo $city; ?><br>
			
			Место работы:  <?php echo $job; ?><br>
			Место учебы:  <?php echo $edu; ?><br>
			
			Интересы: <?php echo $interests; ?> <br>

		</div>	
		<div class="col-lg-4 col-md-4 col-sm-4 col-4">
			<?php 
				$initiator = $_GET['page_id'];
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

				if ($friends_list) {
					echo 'Друзья:<br>';
					foreach ($friends_list as $key => $value) {
						echo $value;
					};
				};
				

				if ($IS_MY_PAGE){

					$query = 'SELECT `first`,`second`,`initiator` FROM `friends` WHERE ((`friends`.`first` = '.$user_id.') or (`friends`.`second` = '.$user_id.')) and (`friends`.`initiator` != 0) and (`friends`.`initiator` != '.$user_id.');';
					$result = mysqli_query($db, $query) or die(mysqli_error($db));

					$applications = array();

					while($line = mysqli_fetch_array($result, MYSQLI_ASSOC)) {
						if ($line['first'] == $user_id) {
							// $applications[] = $line['second'];
							$query = 'SELECT `firstname`,`surname` FROM `accounts` WHERE (`accounts`.`user_id` = '.$line['second'].');';
							$result = mysqli_query($db, $query) or die(mysqli_error($db));
							$row = mysqli_fetch_array($result, MYSQLI_ASSOC);
							$applications[] = '<a href="page.php?page_id='.$line['second'].'">'.$row['firstname']." ".$row['surname'].'</a><br>';
						} else {
							// $applications[] = $line['first'];
							$query = 'SELECT `firstname`,`surname` FROM `accounts` WHERE (`accounts`.`user_id` = '.$line['first'].');';
							$result = mysqli_query($db, $query) or die(mysqli_error($db));
							$row = mysqli_fetch_array($result, MYSQLI_ASSOC);
							$applications[] = '<a href="page.php?page_id='.$line['first'].'">'.$row['firstname']." ".$row['surname'].'</a><br>';
						};

					};

					if ($applications) {
						echo '<br>Заявки в друзья:<br>';
						foreach ($applications as $key => $value) {
							echo $applications[$key].'<br>';
						};
					};
				};



			 ?>


		</div>							
	</div>
</div>
<script type="text/javascript">
	function changeStatus(elem) { 
		elem.onclick = "";
		var status = elem.innerHTML;
		elem.innerHTML = '<form method="post" action="change_status.php"><input type="text" value="'+status+'" name="new_status"> <input type="submit" value="=>"></form>'
	};
</script>
</body>
</html>	