<?php

function lotteria($socket, $channel, $sender, $msg, $infos)
{
	$min = (int)$infos[1];
	$max = (int)$infos[2];

	if($min >= $max) {
		sendmsg($socket, "$sender: Ti pare che $min sia più piccolo di $max??", $channel);
		return;
	}

	$num = rand($min, $max);

	sendmsg($socket, "Ho scelto il numero $num", $channel);
}

?>