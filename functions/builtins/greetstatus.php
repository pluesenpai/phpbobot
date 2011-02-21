<?php
	function greetstatus($socket, $channel, $sender, $msg, $infos)
	{
		global $db;

		if(isset($infos[1])) {
			$iduser = $db->find_user($infos[1]);
			if($iduser > 0) {
				$cond_f = array("user_IDUser", "chan_IDChan");
				$cond_o = array("=", "=");
				$cond_v = array($iduser, $db->find_chan($channel));
				$r = $db->select(array("enter"), array("cangreet"), array(""), $cond_f, $cond_o, $cond_v, 1);
				sendmsg($socket, sprintf(_("greetstatus-userinfo-%s-%s"), $infos[1], (count($r) <= 0 || $db->getBoolFromDB($r[0]["cangreet"]) == false) ? _("greetstatus-not") : ""), $channel); //"L'utente %s se entra in canale %sverr&agrave; salutato"     "NON "
			} else {
				sendmsg($socket, _("greetstatus-unknown_user"), $channel); //"Chi?????"
			}
		} else {
			$cond_f = array("name");
			$cond_o = array("=");
			$cond_v = array($channel);
			$r = $db->select(array("chan"), array("greet"), array(""), $cond_f, $cond_o, $cond_v, 1);
			$r2 = $db->select(array("chan"), array("greetnew"), array(""), $cond_f, $cond_o, $cond_v, 1);
			sendmsg($socket, sprintf(_("greetstatus-onjoin-%s") , $db->getBoolFromDB($r[0]["greet"]) == true ? _("greetstatus-enabled") : _("greetstatus-disabled")), $channel); //"Saluto al join: %s"       "Attivo"         "Disattivo"
			sendmsg($socket, sprintf(_("greetstatus-newusers-%s"), $db->getBoolFromDB($r2[0]["greetnew"]) == true ? _("greetstatus-enabled") : _("greetstatus-disabled")), $channel); //"Saluto ai nuovi utenti: %s"       "Attivo"         "Disattivo"
		}
	}
?>
