<?php

	function set_birthday($socket, $channel, $sender, $msg, $infos)
	{
		global $db;
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

		$giorno = $infos[1];
		$mese = $infos[2];
		if($mese < 1 || $mese > 12) {
			sendmsg($socket, "$mese... che mese &egrave;, $sender???", $channel);
			return;
		}
		if($giorno < 1 || ($mese != 2 && $giorno > $giorni_ammessi[$mese]) || ($mese == 2 && $giorno > 29)) {
			sendmsg($socket, "Non &egrave; possibile questo giorno, $sender!!!", $channel);
			return;
		}
		if(mktime($second, $minute, $hour, $mese, $giorno, (int)date('Y')) > mktime())
			$sum_year = 1;
		else
			$sum_year = 0;

		$year = ((int)date('Y')) + $sum_year;
		$data = date("Y-m-d", mktime($second, $minute, $hour, $mese, $giorno, $year));

		$db->update("user", array("birthday"), array("'$data'"), $cond_f, $cond_o, $cond_v);
	}

?>
