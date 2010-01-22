<?php

function cumbidu($socket, $channel, $sender, $msg, $infos)
{
	global $translations;

	sendmsg($socket, $translations->bot_gettext("joking-cumbidu"), $channel); //"Mi son rotto!!! Adesso offro IO!!!!!"
	notice($socket, $translations->bot_gettext("joking-cumbidu_notice"), $channel); //"cumbida a tutti!! :)"
	//send($socket, "NOTICE $channel :cumbida a tutti!! :)\n");
}

?>
