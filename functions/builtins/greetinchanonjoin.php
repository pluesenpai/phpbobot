<?php
	function greetinchanonjoin($socket, $channel, $sender, $msg, $infos)
	{
		global $db;
		$errore = 0;

		if($infos[1] == "utenti") {
			$table = "chan";
			$field = "greet";
			$cond_f = array("name");
			$cond_o = array("=");
			$cond_v = array($channel);
			if(mb_strtoupper($infos[2]) == "ON") {
				$value = "TRUE";
			} else if(mb_strtoupper($infos[2]) == "OFF") {
				$value = "FALSE";
			} else {
				$errore = 1;
			}
		} else if($infos[1] == "nuovi") {
			$table = "chan";
			$field = "greetnew";
			$cond_f = array("name");
			$cond_o = array("=");
			$cond_v = array($channel);
			if(mb_strtoupper($infos[2]) == "ON") {
				$value = "TRUE";
			} else if(mb_strtoupper($infos[2]) == "OFF") {
				$value = "FALSE";
			} else {
				$errore = 1;
			}
		} else {
			$errore = 1;
		}

		if($errore != 1) {
			$db->update($table, array($field), array($value), $cond_f, $cond_o, $cond_v);
			sendmsg($socket, "Tutto ok!", $channel);
		} else {
			sendmsg($socket, "Errore", $channel);
		}
	}
?>
