<?php
	function addpoke($socket, $channel, $sender, $msg, $infos)
	{
		global $auth, $db;

		if($auth[$sender]) {
			$mess = htmlentities($infos[1], ENT_QUOTES, 'UTF-8');

			$iduser = $db->check_user($sender, $channel);
			$db->insert("poke", array("poke_message", "user_IDUser"), array($mess, $iduser));
			sendmsg($socket, _("addpoke-success"), $channel);
		} else
			notice($socket, _("auth-required"), $sender);
	}
?>

