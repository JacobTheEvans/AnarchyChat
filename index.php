<?php
ini_set("session.cookie_httponly",true);
session_start();

if (isset($_SESSION['last_ip']) == false){
  $_SESSION['last_ip'] = $_SERVER["REMOTE_ADDR"];
}

if ($_SESSION['last_ip'] !== $_SERVER["REMOTE_ADDR"]){
  session_unset();
  session_destroy();
}

if (isset($_SESSION['reload'])){
	session_unset();
	session_destroy();
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
  </div>
  </nav>
  <?php
  if(isset($_SESSION["user_id"])){
  ?>
  <div class="margin-main">
  <div class="container todo">
      <div class="row">
        <div class="col-md-1 col-lg-1 col-sm-1">
          <div class="user-box" id="user-box"></div>
        </div>
        <div class="col-md-11 col-lg-11 col-sm-11">
          <div class="text-box" id="text"></div>
        </div>
      </div>
      <div class="col-md col-lg-12 col-sm-12 space-for-chat">
        <form id="text-input">
        <textarea class="form-control form-custom" placeholder="Message" id="message" name="message"></textarea>
        <br>
        <input type="submit" class="btn btn-custom" value="Submit">
        </form>
      </div>
    </div>
  </div>
</div>
  <?php
  }
  else{
    header("Location: login.php");
  }
  ?>
<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.2/jquery.min.js"></script>

<?php
if(isset($_SESSION["user_id"])){
?>

<script type="text/javascript">
$(document).ready(function(){
  $("#text-input").submit(function(){
    var data ={id: <?php echo $_SESSION['user_id']; ?>,message: $("#message").val(),};
    $.post("core/ajax/addChat.php", data);
    loadChat();
    $("#message").val("");
      return false 
  });
});
</script>

<script type="text/javascript">
function update(){
    $.ajax({
    type: "GET",
    url: "core/ajax/vote.php",
    data: '',
  });
}
</script>

<script type="text/javascript">
function loadUsers(){
	$.ajax({
		type: "GET",
		url: "core/ajax/getUsers.php",
		data: '',
		success: function(msg){
        $("#user-box").html(msg);
		}
	});
}
</script>

<script type="text/javascript">
function loadChat(){
	$.ajax({
		type: "GET",
		url: "core/ajax/getChat.php",
		data: '',
		success: function(msg){
        $('#text').html(msg);
        $("#text").scrollTop($("#text")[0].scrollHeight);
		}
	});
}
</script>

<script type="text/javascript">
  $(document).ready(function(){
    loadChat();
  });
</script>

<script type="text/javascript">
function refreshChat(){
	loadChat();
	loadUsers();
  update();
};

refreshChat(); // This will run on page load
setInterval(function(){
    refreshChat() // this will run after every 5 seconds
}, 500);
</script>

<script type="text/javascript">
$(document).ready(function(){
	window.onbeforeunload = function(e) {
		var data ={id: <?php echo $_SESSION['user_id']; ?>,};
		$.post("core/ajax/updateOnline.php", data);
		$.post("core/ajax/unset.php", data);
	};
});
</script>

<?php
}
?>
</body>
</html>