<?php
	function greetstatus($socket, $channel, $sender, $msg, $infos)
	{
		global $db;

		if(isset($infos[1])) {
			if(is_user_in_chan($infos[1], $channel)) {
				$cond_f = array("user_IDUser", "chan_IDChan");
				$cond_o = array("=", "=");
				$cond_v = array($db->find_user($infos[1]), $db->find_chan($channel));
				$r = $db->select(array("enter"), array("cangreet"), array(""), $cond_f, $cond_o, $cond_v, 1);
				sendmsg($socket, sprintf("L'utente %s se entra in canale %sverr&agrave; salutato", $infos[1], $db->getBoolFromDB($r[0]["cangreet"]) == false ? "NON " : ""), $channel);
			} else {
				sendmsg($socket, "Chi????? Non è in canale!", $channel);
			}
		} else {
			$cond_f = array("name");
			$cond_o = array("=");
			$cond_v = array($channel);
			$r = $db->select(array("chan"), array("greet"), array(""), $cond_f, $cond_o, $cond_v, 1);
			$r2 = $db->select(array("chan"), array("greetnew"), array(""), $cond_f, $cond_o, $cond_v, 1);
			sendmsg($socket, sprintf("Saluto al join: %s", $db->getBoolFromDB($r[0]["greet"]) == true ? "Attivo" : "Disattivo"), $channel);
			sendmsg($socket, sprintf("Saluto ai nuovi utenti: %s", $db->getBoolFromDB($r2[0]["greetnew"]) == true ? "Attivo" : "Disattivo"), $channel);
		}
	}
?>
