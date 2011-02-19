<?php

	function datetime($socket, $channel, $sender, $msg, $infos)
	{
		sendmsg($socket, gmdate("H:i:s O"), $channel);
	}

?>