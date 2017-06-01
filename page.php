<?php
 ini_set('display_errors', E_ALL);
session_start();
require_once 'connection.php';
$db = mysqli_connect($host, $login, $password, $dbname);
mysqli_set_charset($db, "utf8");

// if registration isn't completed or no login
if (isset($_COOKIE['token']) and $_COOKIE['token']!="") {
	$query = 'SELECT `user_id`, `password` FROM `users` WHERE `users`.`token` = "'.$_COOKIE['token'].'" LIMIT 1;';
	    $result = mysqli_query($db, $query);

	    if ($result) {
	        $line = mysqli_fetch_array($result, MYSQLI_ASSOC);
	        $query = 'SELECT `user_id`,`firstname`,`surname`,`bday`,`sex` FROM `accounts` WHERE `accounts`.`user_id` = "'.$line['user_id'].'" LIMIT 1;';
	        $result = mysqli_query($db, $query);

	        if ($result == FALSE) {
	            mysqli_close($db);
	            header("Location: create_account.php");
	            exit;
	        }

	        $line = mysqli_fetch_array($result, MYSQLI_ASSOC);
	        $_SESSION['user_id'] = $line['user_id'];
	        $_SESSION['name'] = $line['firstname']; 
			$_SESSION['surname'] = $line['surname'];
			$_SESSION['bday'] = $line['bday'];
			$_SESSION['gender'] = $line['sex'];
			$username =  $_SESSION['name']." ".$_SESSION['surname'];

	       
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
						<li><a href="#">Редактировать</a></li>
						<li><a href="#"></a></li>
						<li><a href="#"></a></li>
						<li><a href="<?php echo('logout.php') ?>">Выход</a></li>
					</ul>
					<form class="navbar-form navbar-right">
  							<div class="form-group">
						    	<input type="text" class="form-control navbar-right" placeholder="Поиск друзей">
						  	</div>
						  	<button type="submit" class="btn btn-default"><span class="glyphicon glyphicon-search"></span>
</button>
					</form>
				</div>
			</div>
		</div>
	</div>
</div>	

</body>
</html>	