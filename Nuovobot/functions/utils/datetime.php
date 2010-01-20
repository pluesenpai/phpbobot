<?php

	function datetime($socket, $channel, $sender, $msg, $infos)
	{
		if(preg_match("/^time$/", $infos[0])) {
			sendmsg($socket, gmdate("H:i:s O"), $channel);
		} elseif(preg_match("/^date$/", $infos[0])) {
			sendmsg($socket, gmdate("D, d M Y"), $channel);
		} else { //Date and time
			sendmsg($socket, gmdate("r"), $channel);
		}
	}

?>