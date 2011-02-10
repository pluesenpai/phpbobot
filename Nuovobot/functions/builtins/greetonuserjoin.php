<?php
	function greetonuserjoin($socket, $channel, $sender, $msg, $infos)
	{
		global $db;
		$errore = 0;

		$cond_f = array("user_IDUser", "chan_IDChan");
		$cond_o = array("=", "=");
		$cond_v = array($db->find_user($sender), $db->find_chan($channel));
		if(mb_strtoupper($infos[1]) == "ON") {
			$value = "TRUE";
		} else if(mb_strtoupper($infos[1]) == "OFF") {
			$value = "FALSE";
		} else {
			$errore = 1;
		}

		if($errore != 1) {
			$db->update("enter", array("cangreet"), array($value), $cond_f, $cond_o, $cond_v);
			sendmsg($socket, "Tutto ok!", $channel);
		} else {
			sendmsg($socket, "Errore", $channel);
		}
	}
?>
