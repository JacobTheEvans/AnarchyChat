<?php
function checkKickVotes($db){
	$votes = getKickVotes($db);

	foreach ($votes as $vote) {
		//Check if kick vote is expired
		$expired = strtotime("+3 minutes", $vote['time']);
		if($expired <= time()){
			deleteVote($db,$vote['id'],$vote['subject']);
		}
		//Check if user who submitted vote is still online
		$isOnline = count(getCurrnetUser($db,$vote['name']));
		if(!$isOnline){
			deleteVote($db,$vote['id'],$vote['subject']);
		}
	}

	$onlineUsers = count(getUsers($db));

	if(!($onlineUser <= 3)){

		if($onlineUsers <= 10){
			$amount = round($onlineUsers * .75); 
		} elseif($onlineUsers <= 20){
			$amount = round($onlineUsers * .50);
		} elseif($onlineUsers <= 50){
			$amount = round($onlineUsers * .30);
		} elseif($onlineUsers <= 100){
			$amount = round($onlineUsers * .10);
		} else {
			$amount = round($onlineUsers * .7);
		}

		foreach (getUsersToBeKicked($db,$amount) as $user) {
			deleteUserByName($db,$user['name']);
		}
}

}

?>