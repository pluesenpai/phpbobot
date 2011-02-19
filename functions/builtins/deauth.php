<?php
	function deauth($socket, $channel, $sender, $msg, $infos)
	{
		global $auth;

		if($auth[$sender]) {
			$auth[$sender] = false;
			$db->update("user", array("auth"), array("false"), array("username"), array("="), array($sender));
			notice($socket, _("deauth-success"), $sender);
		} else
			notice($socket, _("deauth-failed"), $sender);
	}
?>
