<?php
	function botversion($socket, $channel, $sender, $msg, $infos)
	{
		global $user_name;
		sendmsg($socket, sprintf(_("botversion-%s-%s"), $user_name, version), $channel);
	}
?>

