<?php

	function datetime($socket, $channel, $sender, $msg, $infos)
	{
		sendmsg($socket, gmdate("r"), $channel);
	}

?>