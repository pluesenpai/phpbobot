<?php
	function lists_add($socket, $channel, $sender, $msg, $infos)
	{
		global $allowed_autolists, $registered, $auth, $db;

		if($registered[$sender] || $auth[$sender]) {
			$iduser = $db->check_user($infos[2]);
			$idchan = $db->check_chan($channel);
			$result = $db->select(array("enter"), array("modes"), array(""), array("chan_IDChan", "user_IDUser"), array("=", "="), array($idchan, $iduser));

			if(array_key_exists($infos[1], $allowed_autolists)) {
				$stringa = $allowed_autolists[$infos[1]];

				if(strlen($stringa) >= 1) {
					if(count($result) == 0) {
						$db->insert("enter", array("user_IDUser", "chan_IDChan", "greet_IDGreet", "modes"), array($iduser, $idchan, 0, $stringa));
						sendmsg($socket, sprintf(_("lists-user_added-%s-%s"), $infos[2], $infos[1]), $channel);
					} else {
						if(!preg_match("/$stringa/", $result[0]["modes"])) {
							$stringa = $result[0]["modes"] . $stringa;
							$db->update("enter", array("modes"), array($stringa), array("chan_IDChan", "user_IDUser"), array("=", "="), array($idchan, $iduser));
							sendmsg($socket, sprintf(_("lists-user_added-%s-%s"), $infos[2], $infos[1]), $channel);
						} else
							sendmsg($socket, "lists-mode_present", $channel);
					}
				}
			} else
				sendmsg($socket, sprintf(_("lists-list_notexists-%s"), $infos[1]), $channel);
		} else
			notice($socket, _("auth-required"), $sender);
	}
?>
