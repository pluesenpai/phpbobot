<?php
	function lists_remove($socket, $channel, $sender, $msg, $infos)
	{
		global $allowed_autolists, $registered, $auth;

		if($registered[$sender] || $auth[$sender])) {
			$iduser = $db->check_user($infos[2]);
			$idchan = $db->check_chan($channel);
			$result = $db->select(array("enter"), array("modes"), array(""), array("chan_IDChan", "user_IDUser"), array("=", "="), array($idchan, $iduser));

			if(array_key_exists($infos[1], $allowed_autolists)) {
				$stringa = $allowed_autolists[$infos[1]];

				if(strlen($stringa) >= 1) {
					if(count($result) == 0)
						sendmsg($socket, sprintf(_("lists-user_notpresent-%s-%s"), $infos[2], $infos[1]), $channel);
					else {
						if(strlen($result[0]["modes"]) > 0 && preg_match("/$stringa/", $result[0]["modes"])) {
							$newmode = preg_replace("/$stringa/", "", $result[0]["modes"]);
							if(strlen($newmode) == 1)
								$newmode = "";
							$db->update("enter", array("modes"), array($newmode), array("chan_IDChan", "user_IDUser"), array("=", "="), array($idchan, $iduser));
							sendmsg($socket, sprintf(_("lists-user_removed-%s-%s"), $infos[2], $infos[1]), $channel);
						} else
							sendmsg($socket, sprintf(_("lists-user_notpresent-%s-%s"), $infos[2], $infos[1]), $channel);
					}
				}
			} else
				sendmsg($socket, sprintf(_("lists-list_notexists-%s"), $infos[1]), $channel);
		} else
			notice($socket, _("auth-required"), $sender);
	}
?>
