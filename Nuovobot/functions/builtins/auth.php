<?php
	function auth($socket, $channel, $sender, $msg, $infos)
	{
		global $auth, $db:

		$auth[$sender] = $db->check_password($sender, $infos[1]);
		if($auth[$sender])
			notice($socket, _("auth-success"), $sender);
		else
			notice($socket, _("auth-failed"), $sender);
	}
?>
