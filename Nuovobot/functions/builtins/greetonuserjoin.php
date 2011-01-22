<?php
	function greetonuserjoin($socket, $channel, $sender, $msg, $infos)
	{
		global $db;
		$errore = 0;

		if($infos[1] == "utenti") {
			$table = "chan";
			$field = "greet";
			$cond_f = array("name");
			$cond_o = array("=", "=");
			$cond_v = array($channel);
			if($infos[2] == "ON") {
				$value = "TRUE";
			} else if($infos[2] == "OFF") {
				$value = "FALSE";
			} else {
				$errore = 1;
			}
		} else if($infos[1] == "nuovi") {
			$table = "chan";
			$field = "greetnew";
			$cond_f = array("name");
			$cond_o = array("=", "=");
			$cond_v = array($channel);
			if($infos[2] == "ON") {
				$value = "TRUE";
			} else if($infos[2] == "OFF") {
				$value = "FALSE";
			} else {
				$errore = 1;
			}
		} else {
			$table = "enter";
			$field = "cangreet";
			$cond_f = array("user_IDUser", "chan_IDChan");
			$cond_o = array("=", "=");
			$cond_v = array($db->find_user($sender), $db->find_chan($channel));
			if($infos[3] == "ON") {
				$value = "TRUE";
			} else if($infos[3] == "OFF") {
				$value = "FALSE";
			} else {
				$errore = 1;
			}
		}

		if($errore != 1) {
			$db->update($table, array($field), array($value), $cond_f, $cond_o, $cond_v);
			sendmsg($socket, "Tutto ok!", $channel);
		} else {
			sendmsg($socket, "Errore", $channel);
		}
	}
?>
