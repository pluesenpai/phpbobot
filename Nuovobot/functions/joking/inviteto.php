<?php

function inviteto($socket, $channel, $sender, $msg, $infos)
{
	global $translations;

	//send($socket, "NOTICE $channel :offre un {$infos[1]} a {$infos[2]} :)\n");
	notice($socket, sprintf($translations->bot_gettext("joking-invite-%s-%s"), $infos[1], $infos[2]), $channel); //"offre un {$infos[1]} a {$infos[2]} :)"
}

?>