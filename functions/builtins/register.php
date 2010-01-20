<?php
	function register($socket, $channel, $sender, $msg, $infos)
	{
		global $db;

		if(!$db->user_isregistered($sender))
			$db->add_user($sender, $channel, $infos[1]);
			notice($socket, _("register-complete"), $sender);
		} else {
			notice($socket, _("register-failed"), $sender);
		}
	}
?>
