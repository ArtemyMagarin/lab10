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
    $err_msg = "";
    if (isset($_POST['bday']) and isset($_POST['bmonth']) and isset($_POST['byear'])
    and ($_POST['bday'] != "День") and ($_POST['bmonth'] != "Месяц") and ($_POST['byear']  != "Год")) {
    $min = 1;
    $day_max = 31;
    $month_max = 12;
    $year_max = date("Y") - 14;
    $bday = (int) $_POST['bday'];
    $bmonth = (int) $_POST['bmonth'];
    $byear = (int) $_POST['byear'];

    if (($byear > $year_max) or ($byear < $min)) {
        $err_msg .= '<br/>Укажите корректную дату рождения';
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
            $err_msg .= '<br/>Укажите корректную дату рождения';
        };
    };

    };

    if ($err_msg != "") {
        header("Location: edit_page.php?err=".$err_msg);
        exit;
    };

    $query = 'SELECT `user_id` FROM `users` WHERE `users`.`token` = "'.$_COOKIE['token'].'" LIMIT 1;';
    $result = mysqli_query($db, $query);
   
    if ($result) {
        $line = mysqli_fetch_array($result, MYSQLI_ASSOC);
        $query = 'SELECT `user_id`,`bday`,`sex`,`status`,`marital_status`,`city`,`job`,`edu`,`interests` FROM `accounts` WHERE `accounts`.`user_id` = "'.$line['user_id'].'" LIMIT 1;';
        $result = mysqli_query($db, $query);
   
        if ($result == FALSE) {
            mysqli_close($db);
            header("Location: create_account.php");
            exit;
        }
   
        $line = mysqli_fetch_array($result, MYSQLI_ASSOC);
        $user_id = $line['user_id'];
        $status = $line['status'];
        $marital_status = $line['marital_status'];
        $city = $line['city'];
        $job = $line['job'];
        $edu = $line['edu'];
        $birthday = $line['bday'];
        $gender = $line['sex'];
        $interests = $line['interests'];

    };





    if (isset($_POST['status']) and $_POST['status'] != "") {
        $status = mysqli_real_escape_string($db, $_POST['status']);
    };

    if (isset($_POST['marital_status']) and $_POST['marital_status'] != "") {
        $marital_status = mysqli_real_escape_string($db, $_POST['marital_status']);
    };

    if (isset($_POST['city']) and $_POST['city'] != "") {
        $city = mysqli_real_escape_string($db, $_POST['city']);
    };

    if (isset($_POST['job']) and $_POST['job'] != "") {
        $job = mysqli_real_escape_string($db, $_POST['job']);
    };

    if (isset($_POST['edu']) and $_POST['edu'] != "") {
        $edu = mysqli_real_escape_string($db, $_POST['edu']);
    };

    if (isset($_POST['bday']) and isset($_POST['bmonth']) and isset($_POST['byear'])) {
        $bday = (int) $_POST['bday'];
        $bmonth = (int) $_POST['bmonth'];
        $byear = (int) $_POST['byear'];
        $birthday = $byear.'-'.$bmonth.'-'.$bday;
    };

    if (isset($_POST['gender']) and $_POST['gender'] != "") {
        $gender = mysqli_real_escape_string($db, $_POST['gender']);
    };

    if (isset($_POST['interests']) and $_POST['interests'] != "") {
        $interests = mysqli_real_escape_string($db, $_POST['interests']);
    };

    $query = '
    UPDATE `accounts` SET 
    `status` = "'.$status.'", 
    `marital_status` = "'.$marital_status.'", 
    `city` = "'.$city.'",
    `job` = "'. $job.'",
    `edu` = "'.$edu.'",
    `bday` = "'.$birthday.'",
    `sex` = "'.$gender.'",
    `interests`  = "'.$interests.'" 
    WHERE
    `accounts`.`user_id` = '.$user_id.';
    ';
    
    $result = mysqli_query($db, $query) or die(mysqli_error($db));

    header("Location: page.php");
    exit;


