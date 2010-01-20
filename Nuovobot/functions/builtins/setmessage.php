<?php
	function setmessage($socket, $channel, $sender, $msg, $infos)
	{
		global $auth. $db;

		if($auth[$sender]) {
			$mess = htmlentities($infos[1], ENT_QUOTES, 'UTF-8');

			$db->add_user($sender, $channel, "", $mess);
			sendmsg($socket, _("message-success"), $channel);
		} else
			notice($socket, _("auth-required"), $sender);
	}
?>
