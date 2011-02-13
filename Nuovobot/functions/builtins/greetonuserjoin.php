<?php
	function greetonuserjoin($socket, $channel, $sender, $msg, $infos)
	{
		global $db;
		$errore = 0;

		$cond_f = array("user_IDUser", "chan_IDChan");
		$cond_o = array("=", "=");

		if(isset($infos[3]) && is_bot_op($sender) && ($registered[$sender] || $auth[$sender]) {
			$user = $infos[2];
			$value = $infos[3];
		} else {
			$user = $sender;
			$value = $infos[1];
		}

		if(is_user_in_chan($user)) {
			$cond_v = array($db->find_user($user), $db->find_chan($channel));
			if(mb_strtoupper($value) == "ON") {
				$val = "TRUE";
			} else if(mb_strtoupper($value) == "OFF") {
				$val = "FALSE";
			} else {
				$errore = 1;
			}

			if($errore != 1) {
				$db->update("enter", array("cangreet"), array($val), $cond_f, $cond_o, $cond_v);
				sendmsg($socket, "Tutto ok!", $channel);
			} else {
				sendmsg($socket, "Errore", $channel);
			}
		} else {
			sendmsg($socket, "Chi????? Non è in canale!", $channel);
		}
	}
?>
