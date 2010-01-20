<?php
	function botversion($socket, $channel, $sender, $msg, $infos)
	{
		sendmsg($socket, sprintf(_("botversion-%s-%s"), $user_name, version), $channel);
	}
?>
