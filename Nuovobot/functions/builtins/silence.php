<?php
	function silence($socket, $channel, $sender, $msg, $infos)
	{
		global $parla;

		if($parla[$channel]) {
			sendmsg($socket, "...", $channel);
			$parla[$channel] = false;
		}
	}
?>
