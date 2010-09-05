<?php
	function expression($socket, $channel, $sender, $msg, $infos)
	{
		global $translations;
		$command = "functions/expressions/espr";

		$espr = $infos[1];

		$regex = "^[\(]*[0-9]+([\+\-\*\/%]{1}[\(]*[0-9]+[\)]*)+[\)]*$";

		if(preg_match("/{$regex}|^fuffa$/", $espr)) {
			if($espr == "fuffa") {
				$risultato = "ruffa";
			} else if(is_bot_op($sender)) {
				$risultato = exec("{$command} \"{$espr}\" 1");
			} else {
				$risultato = exec("{$command} \"{$espr}\" 0");
			}
			sendmsg($socket, sprintf($translations->bot_gettext("expressions-result-%s-%s"), $espr, $risultato), $channel); //"Il risultato di $espr &egrave; $risultato"
		} else {
			sendmsg($socket, "Ti sembra una espressione valida? Segui questa regex: {$regex}", $channel);
		}
	}
?>