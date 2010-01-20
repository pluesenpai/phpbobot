<?php
	function expression($socket, $channel, $sender, $msg, $infos)
	{
		$espr = $infos[1];

		if(is_bot_op($sender))
			$risultato = exec("functions/expressions/espr \"$espr\" 1");
		else
			$risultato = exec("functions/expressions/espr \"$espr\" 0");

		sendmsg($socket, "Il risultato di $espr &egrave; $risultato", $channel);
	}
?>