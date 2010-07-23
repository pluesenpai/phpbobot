<?php
	function auth($socket, $channel, $sender, $msg, $infos)
	{
		global $auth, $db;

		$auth[$sender] = $db->check_password($sender, $infos[1]);
		if($auth[$sender]) {
			$db->update("user", array("auth"), array("true"), array("username"), array("="), array($sender));
			notice($socket, _("auth-success"), $sender);
		} else {
			$db->update("user", array("auth"), array("false"), array("username"), array("="), array($sender));
			notice($socket, _("auth-failed"), $sender);
		}
	}
?>
