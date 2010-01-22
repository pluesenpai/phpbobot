<?php

	function set_birthday($socket, $channel, $sender, $msg, $infos)
	{
		global $db, $translations;
		$giorni_ammessi = array(1 => 31,
								3 => 31,
								4 => 30,
								5 => 31,
								6 => 30,
								7 => 31,
								8 => 31,
								9 => 30,
								10 => 31,
								11 => 30,
								12 => 31
							);
		$hour = 0;
		$minute = 0;
		$second = 0;
		$cond_f = array("username");
		$cond_o = array("=");
		$cond_v = array($sender);

		$giorno = $infos[1];
		$mese = $infos[2];
		if($mese < 1 || $mese > 12) {
			sendmsg($socket, sprintf($translations->bot_gettext("birthday-wrong_month-%s-%s"), $mese, $sender), $channel); //"$mese... che mese &egrave;, $sender???"
			return;
		}
		if($giorno < 1 || ($mese != 2 && $giorno > $giorni_ammessi[$mese]) || ($mese == 2 && $giorno > 29)) {
			sendmsg($socket, sprintf($translations->bot_gettext("birthday-wrong_day-%s"), $sender), $channel); //"Non &egrave; possibile questo giorno, $sender!!!"
			return;
		}

		$sum_year = 0;
		if(mktime($second, $minute, $hour, $mese, $giorno, (int)date('Y')) < mktime())
			$sum_year = 1;

		$year = ((int)date('Y')) + $sum_year;
		$data = date("Y-m-d", mktime($second, $minute, $hour, $mese, $giorno, $year));

		$db->update("user", array("birthday"), array($data), $cond_f, $cond_o, $cond_v);

		sendmsg($socket, sprintf($translations->bot_gettext("birthday-ok-%s")), $channel); //"$sender: data impostata."
	}

?>
