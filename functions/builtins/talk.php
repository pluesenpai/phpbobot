<?php
	function talk($socket, $channel, $sender, $msg, $infos)
	{
		global $parla;

		if(!$parla[$channel]) {
			$parla[$channel] = true;
			sendmsg($socket, _("talk-message"), $channel);
		}
	}
?>
