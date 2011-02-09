<?php

function dado($socket, $channel, $sender, $msg, $infos)
{
	global $translations;

	$n = (int)$infos[1];
	$min = 1;
	$max = 5;

	if($n < $min || $n > $max) {
		sendmsg($socket, sprintf($translations->bot_gettext("joking-dado_err-%s-%s-%s"), $sender, $min, $max), $channel);
		return;
	}

	$num = 0;
	for($i = 0; $i < $n; $i++) {
		srand(time());
		$num += rand(1, 6);
	}

	sendmsg($socket, sprintf($translations->bot_gettext("joking-dado_extracted-%s"), $num), $channel);
}

?>