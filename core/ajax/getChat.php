<?php
require("../php/db.php");

foreach(getChat($db) as $chat) {
	echo("<h4 style='margin-bottom: 0px;'>");
	echo( "<span class='sep'></span>" . $chat['name'] . ": ");
	echo("<span class='time pull-right'> " . date("g:i", ($chat['time'])) . " </span>");
	echo($chat['message']);
	echo("</h4>");
	echo("<hr>");
}
?>