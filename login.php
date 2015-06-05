<?php
require("core/php/db.php");

ini_set("session.cookie_httponly",true);
session_start();

if (isset($_SESSION['last_ip']) == false){
  $_SESSION['last_ip'] = $_SERVER["REMOTE_ADDR"];
}

if ($_SESSION['last_ip'] !== $_SERVER["REMOTE_ADDR"]){
  session_unset();
  session_destroy();
}

if(isset($_POST['name'])){
  $name = $_POST['name'];
  $ip = $_SESSION['last_ip'];
  $expires = strtotime("+20 minutes",time());
  $user_id = rand();

  //Check if user already exists
  $users = getCurrnetUser($db,$name);

  if(count($users)){
    echo("<script>alert('This nickname is already in use, please user another.');</script>");
  }else{
    addUser($db,$user_id,$name,$ip,$expires);
    $_SESSION['user_id'] = $user_id ;

    //Display likeable level
    $info = getUserVotes($db,$name)[0];
    $level += $info['likes'];
    $level -= $info['prick'];

    if($level >= 3){
      $result = 'likeable';
    } elseif($level <= -3){
      $result = 'a prick';
    } else {
      $result = 'netural';
    }

    addChat($db,"SERVER",$name . " has joined and is " . $result . ".");
  }

  if(isset($_SESSION['user_id'])){
    header("Location: index.php");
  }
}
?>


<!DOCTYPE html>
<html>
<head>
	<title>Anarchy Chat</title>
  <meta charset="utf-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link href="core/css/style.css" rel="stylesheet">
  <link href='http://fonts.googleapis.com/css?family=Ubuntu' rel='stylesheet' type='text/css'>
  <link href="core/css/bootstrap.min.css" rel="stylesheet">
</head>

<body>
<nav class="navbar navbar-inverse navbar-fixed-top" role="navigation">
    <div class="navbar-header">
      <button type="button" class="navbar-toggle" data-toggle="collapse" data-target=".navbar-collapse">
        <span class="sr-only">Toggle navigation</span>
        <span class="icon-bar"></span>
        <span class="icon-bar"></span>
        <span class="icon-bar"></span>
      </button>
    </div>
  <!--This is what will be shown on the computer-->
  <div class="navbar-collapse collapse navbar-centered">
    <ul class="nav navbar-nav">
      <li class="head-link"><div class='icon-padding'><a href="index.php"><img class='icon' src="core/images/icon.png"></a></div></li>
    </ul>
    <a href="login.php" class="pull-right bar-text"><div>Anarchy Chat</div></a>
  </div>
  </nav>
  <div class="bor">
  <div class="container">
      <div class="row login">
        <div class=" col-lg-offset-2 col-md-9 col-lg-9 col-sm-9 col-sm-offset-2 todo">
        <br>
        <h3>Welcome To Anarchy Chat</h3>
        <h4>Please login</h4>
        <form action="" id="login" method="post">
          <input class="form-control login-custom" placeholder="Nickname" name="name" id="name"></input>
          <input type="submit" class="btn btn-custom-login" value="Login">
        </form>
        <h3>Info</h3>
        <p class="login-p">
        Anarchy chat has no moderators. If you have an issue with another member please use the commands below. All commands are based on the votes of the users in the chat.
        </p>
        <h3>Commands</h3>
        <p class="login-p">
        !kick @username
        <br>
        !like @username
        <br>
        !prick @username
        </p>
      </div>
    </div>
    </div>
<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.2/jquery.min.js"></script>
</body>
</html>