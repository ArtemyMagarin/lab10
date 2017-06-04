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
   
   // if registration isn't completed or no login
   if (isset($_COOKIE['token']) and $_COOKIE['token']!="") {
      $query = 'SELECT `user_id`, `password` FROM `users` WHERE `users`.`token` = "'.$_COOKIE['token'].'" LIMIT 1;';
          $result = mysqli_query($db, $query);
   
          if ($result) {
              $line = mysqli_fetch_array($result, MYSQLI_ASSOC);
      $query = 'SELECT `user_id`,`firstname`,`surname`,`bday`,`sex`,`status`,`marital_status`,`city`,`job`,`edu`,`interests` FROM `accounts` WHERE `accounts`.`user_id` = "'.$line['user_id'].'" LIMIT 1;';
              $result = mysqli_query($db, $query);
   
              if ($result == FALSE) {
                  mysqli_close($db);
                  header("Location: create_account.php");
                  exit;
              }
   
              $line = mysqli_fetch_array($result, MYSQLI_ASSOC);
              foreach ($line as $key => $value) {
                  if (gettype($value) == 'NULL') {
                      $line[$key] = "не указано";
                  };
              };
              $_SESSION['user_id'] = $line['user_id'];
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
      <title>Редактировать - <?php echo $username ?> </title>
      <link rel="icon" href="images/favicon.ico" type="image/x-icon">
      <link rel="shortcut icon" href="images/favicon.ico" type="image/x-icon">
      <link rel="stylesheet" type="text/css" href="css/bootstrap.min.css">
      <!-- <link rel="stylesheet" href="css/page.css" type="text/css" /> -->
      <link href='http://fonts.googleapis.com/css?family=Open+Sans:400,700' rel='stylesheet' type='text/css'>
      <style type="text/css">
         input[type="radio"] {
         -webkit-appearance: radio;
         -moz-appearance: radio;
         height: 13px;
         max-width: 13px;
         font-size: 14px;
         outline: none;
         -webkit-appearance:none;
         width: 350px;
         align: center;
         position: center;
         }
         ul.gender {
         list-style: none; 
         margin-bottom: 0px; 
         padding-left: 0;
         text-align: justify;
         }
         ul.gender li {
         margin-right: 10px;
         display: inline;
         color: #000000;
         text-align: center;
         }
      </style>
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
                           echo ('
                               <li><a href="page.php">Моя страница</a></li>
                               <li><a href="logout.php">Выход</a></li>
                           ');
                           
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
            <div class="col-lg-3 col-md-3 col-sm-3 col-3">
            </div>
            <div class="col-lg-6 col-md-6 col-sm-6 col-6">
            
            <?php 
                if (isset($_GET['err'])) {
                    echo('<div class="alert alert-danger">'.$_GET['err']).'</div>';
                };
             ?>
            
               <form method="post" action="edit_info.php">
                  <div class="form-group">
                     <label>Пол:</label>
                     <ul class="gender">
                        <div class="row">
                           <div class="col-lg-4 col-md-4 col-sm-4 col-4">
                              <center>
                                 <li><label><input type="radio" name="gender" value="female">Я женщина</label></li>
                              </center>
                           </div>
                           <div class="col-lg-4 col-md-4 col-sm-4 col-4">
                              <center>
                                 <li><label><input type="radio" name="gender" value="male">Я мужчина</label></li>
                              </center>
                           </div>
                           <div class="col-lg-4 col-md-4 col-sm-4 col-4">
                              <center>
                                 <li><label><input type="radio" name="gender" value="other">Другое</label></li>
                              </center>
                           </div>
                        </div>
                     </ul>
                  </div>
                  <div class="form-group">
                     <label>Дата рождения:</label>
                     <center>
                        <select id='bday' name="bday" onclick="day_filler(document.getElementById('bmonth').value, document.getElementById('byear').value, document.getElementById('bday').value)">
                        </select>
                        <select id='bmonth' name="bmonth" onclick="day_filler(document.getElementById('bmonth').value, document.getElementById('byear').value, document.getElementById('bday').value)">
                        </select>
                        <select id='byear' name="byear" onclick="day_filler(document.getElementById('bmonth').value, document.getElementById('byear').value, document.getElementById('bday').value)">
                        </select>
                     </center>
                  </div>
                  <div class="form-group">
                     <label for="sp">Семейное положение:</label>
                     <input type="text" class="form-control" id="sp" name="marital_status">
                  </div>
                  <div class="form-group">
                     <label for="city">Город:</label>
                     <input type="text" class="form-control" id="city" name="city">
                  </div>
                  <div class="form-group">
                     <label for="job">Место работы:</label>
                     <input type="text" class="form-control" id="job" name="job">
                  </div>
                  <div class="form-group">
                     <label for="edu">Место учебы:</label>
                     <input type="text" class="form-control" id="edu" name="edu">
                  </div>
                  <div class="form-group">
                     <label for="int">Интересы:</label>
                     <textarea class="form-control" id="int" name="interests"></textarea>
                  </div>
                  <center><button type="submit" class="btn btn-default">Отправить</button></center>
               </form>
            </div>
            <div class="col-lg-3 col-md-3 col-sm-3 col-3">
            </div>
         </div>
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
