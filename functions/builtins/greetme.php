<?php
	function greetme($socket, $channel, $sender, $msg, $infos)
	{
		sendmsg($socket, sprintf(_("greetme-message-%s"), $sender), $channel);
	}
?>
