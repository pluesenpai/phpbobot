<?php
	function greetonuserjoin($socket, $channel, $sender, $msg, $infos)
	{
		global $db;
		$errore = 0;

		$cond_f = array("user_IDUser", "chan_IDChan");
		$cond_o = array("=", "=");

		if(isset($infos[3]) && is_bot_op($sender) && ($registered[$sender] || $auth[$sender])) {
			$user = $infos[2];
			$value = $infos[3];
		} else {
			$user = $sender;
			$value = $infos[1];
		}

		if(is_user_in_chan($user, $channel)) {
			$cond_v = array($db->find_user($user), $db->find_chan($channel));
			if(mb_strtoupper($value) == "ON") {
				$val = "TRUE";
			} else if(mb_strtoupper($value) == "OFF") {
				$val = "FALSE";
			} else {
				$errore = 1;
			}

			if($errore != 1) {
				$r = $db->select(array("enter"), array("cangreet"), array(""), $cond_f, $cond_o, $cond_v, 1);
				if(count($r) > 0)
					$db->update("enter", array("cangreet"), array($val), $cond_f, $cond_o, $cond_v);
				else
					$db->insert("enter", array("user_IDUser", "chan_IDChan", "greet_IDGreet", "modes", "kicks", "cangreet"), array($db->find_user($user), $db->find_chan($channel), 0, "", 0, "TRUE"));
				sendmsg($socket, "Tutto ok!", $channel);
			} else {
				sendmsg($socket, "Errore", $channel);
			}
		} else {
			sendmsg($socket, "Chi????? Non è in canale!", $channel);
		}
	}
?>
