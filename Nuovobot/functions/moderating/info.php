<?php

function info($socket, $channel, $sender, $msg, $infos)
{
	global $moderated, $translations;

	if($moderated[$channel])
		sendmsg($socket, $translations->bot_gettext("moderating-infos-yes"), $channel); //"Sto moderando il canale!!!"
	else
		sendmsg($socket, $translations->bot_gettext("moderating-infos-no"), $channel); //"NON sto moderando il canale!!!"
}

?>