<?php
$config['db'] = array(
	'host' => 'localhost',
	'username' => 'username',
	'password' => 'password',
	'dbname' => 'dbname'
);

try{
	$db = new PDO("mysql:host=". $config['db']['host'] . ";dbname=" . $config['db']['dbname'], $config['db']['username'], $config['db']['password']);
	$db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
}catch (PDOException $e){
	die("[-] Failed to Connect to Database: " . $config['db']['dbname']);
} 

function createTableUsers($db,$table){
	//Create Main Users
	$query = $db->prepare("CREATE TABLE " . $table . "(id INT NOT NULL PRIMARY KEY AUTO_INCREMENT, user_id varchar(140), name varchar(26), ip varchar(250), expires INT(11), online varchar(25));");
	$query->execute();
}

function createTableRoom($db,$table){
	//Create Main Room
	$query = $db->prepare("CREATE TABLE " . $table . "(id INT NOT NULL PRIMARY KEY AUTO_INCREMENT, name varchar(26), message varchar(250) , time INT(11));");
	$query->execute();
}

function createTableUserVotes($db,$table){
	$query = $db->prepare("CREATE TABLE " . $table . "(id INT NOT NULL PRIMARY KEY AUTO_INCREMENT, name varchar(26), kick int(11) , likes int(11), prick int(11));");
	$query->execute();
}

function createTableVotes($db,$table){
	$query = $db->prepare("CREATE TABLE " . $table . "(id INT NOT NULL PRIMARY KEY AUTO_INCREMENT, name varchar(26), type varchar(50) , subject varchar(50), time int(11));");
	$query->execute();
}

function addChat($db,$name,$text){
	//Santize Input
	$name = htmlentities($name, ENT_COMPAT, 'UTF-8');
	$text = htmlentities($text, ENT_COMPAT, 'UTF-8');

	$timestamp = time();
	$query = $db->prepare("Insert into room values (NULL, '{$name}', '{$text}',{$timestamp});");
	$query->execute();
}

function addUser($db,$id,$name,$ip,$expires){
	//Santize Input
	$id = htmlentities($id, ENT_COMPAT, 'UTF-8');
	$name = htmlentities($name, ENT_COMPAT, 'UTF-8');
	$ip = htmlentities($ip, ENT_COMPAT, 'UTF-8');
	$expires = htmlentities($expires, ENT_COMPAT, 'UTF-8');

	$query = $db->prepare("Insert into users values (NULL, '{$id}', '{$name}', '{$ip}',{$expires},'true');");
	$query->execute();

	$query = $db->prepare("Insert into user_votes values (NULL, '{$name}', 0 , 0 , 0);");
	$query->execute();
}

function addVote($db,$user,$type,$subject){
	//Santize Input
	$user = htmlentities($user, ENT_COMPAT, 'UTF-8');
	$type = htmlentities($type, ENT_COMPAT, 'UTF-8');
	$subject = htmlentities($subject, ENT_COMPAT, 'UTF-8');

	//Get time post was added
	$time = time();

	if(!checkVoteExists($db,$user,$subject,$type)){
		$query = $db->prepare("Insert into votes values (NULL, '{$user}', '{$type}','{$subject}',{$time});");
		$query->execute();

		if($type == "!kick"){
			updateKick($db,$subject);

		}elseif($type == "!like") {
			updateLike($db,$subject);

		}elseif($type == "!prick") {
			updatePrick($db,$subject);
		}

		return true;
	}

	return false;
}

function deleteChat($db,$item){
	//Santize Input
	$item = htmlentities($item, ENT_COMPAT, 'UTF-8');

	$query = $db->prepare("Delete from room where id='" . $item . "';");
	$query->execute();
}

function deleteUser($db,$item){
	//Santize Input
	$item = htmlentities($item, ENT_COMPAT, 'UTF-8');

	$query = $db->prepare("Delete from users where id='" . $item . "';");
	$query->execute();
}

function deleteUserByName($db,$name){
	//Santize Input
	$name = htmlentities($name, ENT_COMPAT, 'UTF-8');

	$query = $db->prepare("Delete from users where name='" . $name . "';");
	$query->execute();
}


function deleteVote($db,$id,$name){
	//Santize Input
	$id = htmlentities($id, ENT_COMPAT, 'UTF-8');

	$query = $db->prepare("Delete from votes where id='" . $id . "';");
	$query->execute();

	$query = $db->prepare("update user_votes set kick = kick-1 where name='{$name}';");
	$query->execute();
}

function getChat($db){
	$query = $db->prepare("select * from room");
	$query->execute();
	$rows = $query->fetchAll(PDO::FETCH_ASSOC);
	return $rows;
}

function getUsers($db){
	$query = $db->prepare("select * from users");
	$query->execute();
	$rows = $query->fetchAll(PDO::FETCH_ASSOC);
	return $rows;
}

function getUser($db,$name){
	//Santize Input
	$name = htmlentities($name, ENT_COMPAT, 'UTF-8');

	$query = $db->prepare("select * from users where name='{$name}'");
	$query->execute();
	$rows = $query->fetchAll(PDO::FETCH_ASSOC);
	return $rows;
}

function getCurrnetUsers($db){
	$time = time();
	$query = $db->prepare("select * from users where expires>{$time} and online='true'");
	$query->execute();
	$rows = $query->fetchAll(PDO::FETCH_ASSOC);
	return $rows;
}

function getCurrnetUser($db,$name){
	//Santize Input
	$name = htmlentities($name, ENT_COMPAT, 'UTF-8');

	$time = time();
	$query = $db->prepare("select * from users where name='{$name}' and expires>{$time} and online='true'");
	$query->execute();
	$rows = $query->fetchAll(PDO::FETCH_ASSOC);
	return $rows;
}

function getUserFromID($db,$id){
	//Santize Input
	$id = htmlentities($id, ENT_COMPAT, 'UTF-8');

	$time = time();
	$query = $db->prepare("select * from users where user_id='{$id}' and expires>{$time} and online='true'");
	$query->execute();
	$rows = $query->fetchAll(PDO::FETCH_ASSOC);
	return $rows;
}

function getUserVotes($db,$user){
	//Santize Input
	$user = htmlentities($user, ENT_COMPAT, 'UTF-8');

	$query = $db->prepare("select * from user_votes where name='{$user}'");
	$query->execute();
	$rows = $query->fetchAll(PDO::FETCH_ASSOC);
	return $rows;
}

function getUsersToBeKicked($db,$amount){
	//Santize Input
	$amount = htmlentities($amount, ENT_COMPAT, 'UTF-8');

	$query = $db->prepare("select * from user_votes where kick >={$amount}");
	$query->execute();
	$rows = $query->fetchAll(PDO::FETCH_ASSOC);
	return $rows;
}


function getVotes($db){
	$query = $db->prepare("select * from votes");
	$query->execute();
	$rows = $query->fetchAll(PDO::FETCH_ASSOC);
	return $rows;
}

function getKickVotes($db){
	$query = $db->prepare("select * from votes where type='!kick'");
	$query->execute();
	$rows = $query->fetchAll(PDO::FETCH_ASSOC);
	return $rows;
}

function getAmountOfOnlineUsers($db){
	$query = $db->prepare("select * from users where online='true'';");
	$query->execute();
	$rows = $query->fetchAll(PDO::FETCH_ASSOC);

	return(count($rows));
}


function checkVoteExists($db,$user,$subject,$type){
	//Santize Input
	$user = htmlentities($user, ENT_COMPAT, 'UTF-8');
	$subject = htmlentities($subject, ENT_COMPAT, 'UTF-8');
	$type = htmlentities($type, ENT_COMPAT, 'UTF-8');

	$query = $db->prepare("select * from votes where name='{$user}' and subject='{$subject}' and type='{$type}';");
	$query->execute();
	$rows = $query->fetchAll(PDO::FETCH_ASSOC);

	//If rows is not zero vote already exists
	if(count($rows)){
		return true;
	}else {
		return false;
	}
}

function updateOnline($db,$id){
	//Santize Input
	$id = htmlentities($id, ENT_COMPAT, 'UTF-8');

	$time = time();
	$query = $db->prepare("update users set online='false' where user_id={$id};");
	$query->execute();
}

function updateLike($db,$user){
	//Santize Input
	$user = htmlentities($user, ENT_COMPAT, 'UTF-8');

	$query = $db->prepare("update user_votes set likes = likes+1 where name='{$user}';");
	$query->execute();

}

function updatePrick($db,$user){
	//Santize Input
	$user = htmlentities($user, ENT_COMPAT, 'UTF-8');

	$query = $db->prepare("update user_votes set prick = prick+1 where name='{$user}';");
	$query->execute();
}

function updateKick($db,$user){
	//Santize Input
	$user = htmlentities($user, ENT_COMPAT, 'UTF-8');

	$query = $db->prepare("update user_votes set kick = kick+1 where name='{$user}';");
	$query->execute();
	
}

//Check if table users exists
$query = $db->prepare("SHOW TABLES LIKE 'users'");
$query->execute();
$rows = $query->fetchAll(PDO::FETCH_ASSOC);

if (empty($rows)){
	createTableUsers($db,"users");
}

//Check if table room exists
$query = $db->prepare("SHOW TABLES LIKE 'room'");
$query->execute();
$rows = $query->fetchAll(PDO::FETCH_ASSOC);

if (empty($rows)){
	createTableRoom($db,"room");
}

//Check if table user_votes exists
$query = $db->prepare("SHOW TABLES LIKE 'user_votes'");
$query->execute();
$rows = $query->fetchAll(PDO::FETCH_ASSOC);

if (empty($rows)){
	createTableUserVotes($db,"user_votes");
}

//Check if table votes exists
$query = $db->prepare("SHOW TABLES LIKE 'votes'");
$query->execute();
$rows = $query->fetchAll(PDO::FETCH_ASSOC);

if (empty($rows)){
	createTableVotes($db,"votes");
}
?>
