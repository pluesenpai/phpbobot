<?php
	function deauth($socket, $channel, $sender, $msg, $infos)
	{
		global $auth;

		if($auth[$sender]) {
			$auth[$sender] = false;
			notice($socket, _("deauth-success"), $sender);
		} else
			notice($socket, _("deauth-failed"), $sender);
	}
?>
