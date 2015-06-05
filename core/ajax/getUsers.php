<?php
require("../php/db.php");

echo("<h3 class='user-logo'>Users</h3>");
echo("<ul class='user-list'>");
foreach (getCurrnetUsers($db) as $user) {
	echo("<li><p>");
	echo($user['name']);
	echo("</p></li>");
}
echo("</ul>");

?>