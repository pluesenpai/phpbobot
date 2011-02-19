<?php
	function greet($socket, $channel, $sender, $msg, $infos)
	{
		sendmsg($socket, _("greet-message"), $channel);
	}
?>
