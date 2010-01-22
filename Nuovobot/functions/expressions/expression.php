<?php
	function expression($socket, $channel, $sender, $msg, $infos)
	{
		global $translations;

		$espr = $infos[1];

		if(is_bot_op($sender))
			$risultato = exec("functions/expressions/espr \"$espr\" 1");
		else
			$risultato = exec("functions/expressions/espr \"$espr\" 0");

		sendmsg($socket, sprintf($translations->bot_gettext("expressions-result-%s-%s"), $espr, $risultato), $channel); //"Il risultato di $espr &egrave; $risultato"
	}
?>