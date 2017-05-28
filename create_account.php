<?php 
session_start();
$err_msg = '';

require_once 'connection.php';
$db = mysqli_connect($host, $login, $password, $dbname);
mysqli_set_charset($db, "utf8");

if (!isset($_SESSION['register_passed'])) {
	header("Location: register.php");
};
if (isset($_POST['firstname']) and isset($_POST['surname']) and isset($_POST['gender'])) {
	$firstname = mysqli_real_escape_string($db, $_POST['firstname']);
	$surname = mysqli_real_escape_string($db, $_POST['surname']);
	$gender = mysqli_real_escape_string($db, $_POST['gender']);
	$birthday = 'NULL';
} else {
	$err_msg = 'Укажите свои имя, фамилию и пол';
};

if (isset($_POST['bday']) and isset($_POST['bmonth']) and isset($_POST['byear'])) {
	$min = 1;
	$day_max = 31;
	$month_max = 12;
	$year_max = date("Y") - 14;
	$bday = (int) $_POST['bday'];
	$bmonth = (int) $_POST['bmonth'];
	$byear = (int) $_POST['byear'];
	if (($byear > $year_max) or ($byear < $min)) {
		$err_msg = 'Укажите корректную дату рождения';
	} else {
		if (!(($bmonth == 1) or ($bmonth == 3) or ($bmonth == 5) or ($bmonth == 7) or ($bmonth == 8) or ($bmonth == 10) or ($bmonth == 12))) {
			$day_max = 30;
		};

		if ($bmonth == 2) {
			if ((($byear%4==0) && ($byear%100!=0)) || ($byear%400 == 0)) { 
				$day_max = 29;
			} else {
				$day_max = 28;
			};
		};

		if (($bday > $day_max) or ($bday < $min) or ($bmonth > $month_max) or ($bmonth < $min)) {
			$err_msg = 'Укажите корректную дату рождения';
		};
	};
};

	if ($err_msg == '') {
		if (isset($_POST['bday']) and isset($_POST['bmonth']) and isset($_POST['byear'])) {
			$birthday = $byear.'-'.$bmonth.'-'.$bday;
		};
		$query = 'INSERT INTO accounts (`user_id`, `firstname`, `surname`, `sex`, `bday`) VALUES ("'.$_SESSION['user_id'].'", "'.$firstname.'", "'.$surname.'", "'.$gender.'", "'.$birthday.'");';
    	$result = mysqli_query($db, $query);

        $token = uniqid('', TRUE);
        mysqli_query($db, 'UPDATE `users` SET `token`="'.$token.'" WHERE `user_id`="'.$_SESSION['user_id'].'";');
        setcookie('token', $token, time()+(60*60*12));
        unset($_SESSION['register_passed']);
        mysqli_close($db);
        header("Location: page.php");
        exit;
	};


	mysqli_close($db);
 ?>

 <!DOCTYPE html>
 <html>
 <head>
 	<title>Расскажите о себе</title>
 	<meta charset="UTF-8">
    <link rel="icon" href="images/favicon.ico" type="image/x-icon">
    <link rel="shortcut icon" href="images/favicon.ico" type="image/x-icon">
    <link rel="stylesheet" href="css/style.css" media="screen" type="text/css" />
    <link href='http://fonts.googleapis.com/css?family=Open+Sans:400,700' rel='stylesheet' type='text/css'>
 </head>
 <body>
 <div id="about-me-form">
  	<h1>Немного обо мне:</h1>
 		<fieldset>
		 	<form method="post">
		 		<input type="text" name="firstname" autofocus="" required="" class="top" placeholder="Имя">
		 		<input type="text" name="surname" class="bottom" required="" placeholder="Фамилия">
		 		<ul class="gender">
			 		<li><input type="radio" name="gender" required="" value="female">Я женщина</li>
			 		<li><input type="radio" name="gender" required="" value="male">Я мужчина</li>
			 		<li><input type="radio" name="gender" required="" value="other">Другое</li>
		 		</ul>
		 		<p>Мой день рождения:</p>
		 		<select id='bday' name="bday" onclick="day_filler(document.getElementById('bmonth').value, document.getElementById('byear').value, document.getElementById('bday').value)">
		 			
		 		</select>
		 		<select id='bmonth' name="bmonth" onclick="day_filler(document.getElementById('bmonth').value, document.getElementById('byear').value, document.getElementById('bday').value)">
		 			
		 		</select>
		 		<select id='byear' name="byear" onclick="day_filler(document.getElementById('bmonth').value, document.getElementById('byear').value, document.getElementById('bday').value)">
		 			
		 		</select>
		 		<p class="error"><?php echo $err_msg ?></p>
		 		<input type="submit" value="Опубликовать">
 			</form>
 		</fieldset>
 </div>

 <script type="text/javascript">
 	function years_filler() {
 		document.getElementById('byear').innerHTML = '';
 		var new_option = document.createElement("option");
 		new_option.disabled = ' ';
 		new_option.selected = ' ';
 		new_option.hidden = ' ';
 		new_option.innerHTML = 'Год';
		document.getElementById('byear').appendChild(new_option);
 		var date = new Date();
 		for (var i = date.getFullYear()-14; i >= date.getFullYear()-100; i--) {
 			var new_option = document.createElement("option");
 			new_option.name = 'year';
 			new_option.value = i.toString();
 			new_option.innerHTML = i.toString();
 			document.getElementById('byear').appendChild(new_option);
 		};
 	};

 	function month_filler() {
 		document.getElementById('bmonth').innerHTML = '';
 		var new_option = document.createElement("option");
 		new_option.disabled = ' ';
 		new_option.selected = ' ';
 		new_option.hidden = ' ';
 		new_option.innerHTML = 'Месяц';
		document.getElementById('bmonth').appendChild(new_option);
 		var months = ['Января', 'Февраля', 'Марта', 'Апреля', 'Мая', 'Июня', 'Июля', 'Августа', 'Сентября', 'Октября', 'Ноября', 'Декабря'];

 		for (var i = 0; i <= months.length - 1; i++) {
 			var new_option = document.createElement("option");
 			new_option.name = 'month';
 			new_option.value = i+1;
 			new_option.innerHTML = months[i];
 			document.getElementById('bmonth').appendChild(new_option);
 		};
 	};

 	function day_filler(month, year, selected_day) {
 		var fin_day = 0;
 		if (['1', '3', '5', '7', '8', '10', '12', 'Месяц'].includes(month)) {
 			fin_day = 31;
 		} else {
 			fin_day = 30;
 		};

 		if (month == 2) {
 			if (((year%4==0) && (year%100!=0)) || (year%400 == 0)) {
 				fin_day = 29;
 			} else {
 				fin_day = 28;
 			};
 		};

 		
 		document.getElementById('bday').innerHTML = '';
 		var new_option = document.createElement("option");
 		new_option.disabled = ' ';
 		new_option.selected = ' ';
 		new_option.hidden = ' ';
 		new_option.innerHTML = 'День';
		document.getElementById('bday').appendChild(new_option);

 		for (var i = 1; i <= fin_day; i++) {
 			var new_option = document.createElement("option");
 			new_option.name = 'day';
 			new_option.value = i;
 			new_option.innerHTML = i.toString();
 			document.getElementById('bday').appendChild(new_option);
 		};
 		if (selected_day <= fin_day) {
 			document.getElementById('bday').value = selected_day;
 		} else {
			document.getElementById('bday').value = 'День';
 		}
	 	
 	};

 	window.onload = function() {
 		years_filler();
 		month_filler();
 		day_filler(1, 1998, 100);
 	};
 </script>	
 </body>
 </html>