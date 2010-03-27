<?php

	function datetime($socket, $channel, $sender, $msg, $infos)
	{
		sendmsg($socket, gmdate("D, d M Y"), $channel);
	}

?>