<?php
require("../php/db.php");

$id = $_POST['id'];

updateOnline($db,$id);
deleteUser($db,$id);
?>