<?php

function lotteria($socket, $channel, $sender, $msg, $infos)
{
	global $translations;

	$min = (int)$infos[1];
	$max = (int)$infos[2];

	if($min >= $max) {
		sendmsg($socket, sprintf($translations->bot_gettext("joking-lotteria_err-%s-%s-%s"), $sender, $min, $max), $channel); //"$sender: Ti pare che $min sia più piccolo di $max??"
		return;
	}

	$num = rand($min, $max);

	sendmsg($socket, sprintf($translations->bot_gettext("joking-lotteria_chosen-%s"), $num), $channel); //"Ho scelto il numero $num"
}

?>