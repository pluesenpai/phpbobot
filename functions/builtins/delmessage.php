<?php
	function delmessage($socket, $channel, $sender, $msg, $infos)
	{
		global $auth, $db;

		if($auth[$sender]) {
			$db->del_greet($sender, $channel);
			sendmsg($socket, _("message-removed"), $channel);
		} else
			notice($socket, _("auth-required"), $sender);
	}
?>
