<?php
require("../php/db.php");
require("../php/execute.php");

$id = $_POST['id'];
$text = $_POST['message'];
$user = getUserFromID($db,$id);

$commands = ["!kick","!like","!prick"];
$isCommand = false;

foreach ($commands as $command) {
	if(strpos($text,$command) !== false){
		$isCommand = true;
	}
}

if(count($user) && !($isCommand)){
	addChat($db,$user[0]['name'],$text);
}

if(($isCommand) && (count($user))){
	execute($db,$user[0]['name'],$text);
}

?>