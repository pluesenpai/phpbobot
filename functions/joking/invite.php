<?php

function invite($socket, $channel, $sender, $msg, $infos)
{
	global $translations;

	//send($socket, "NOTICE $channel :offre un {$infos[1]} a $sender :)\n");
	notice($socket, sprintf($translations->bot_gettext("joking-invite-%s-%s"), $infos[1], $sender), $channel); //"offre un {$infos[1]} a $sender :)"
}

?>