<?php 

function execute($db,$user,$command){

	if(strpos($command,"!kick") !== false){
		$subject = str_replace("!kick" , "", $command);

		$result = addVote($db,$user,"!kick",substr($subject, 2));

		if($result){
			addChat($db,"SERVER", $user . " has voted to kick " . $subject . ".");

			//follow chat to kick
			
			//$amount = getAmountOfOnlineUsers($db);
			//$votes = getKicks

			//if($user)
		}
	}

	elseif(strpos($command,"!like") !== false){
		$subject = str_replace("!like" , "", $command);

		$result = addVote($db,$user,"!like",substr($subject, 2));

		if($result){
			addChat($db,"SERVER", $user . " has voted that " . $subject . " is likable.");
		}

		
	}

	elseif(strpos($command,"!prick") !== false){
		addChat($db,"Server", "!prick Has been called");
		$subject = str_replace("!prick" , "", $command);

		$result = addVote($db,$user,"!prick",substr($subject, 2));


		if($result){
			addChat($db,"SERVER", $user . " has voted that " . $subject . " is a prick.");
		}
	}
	
}

?>