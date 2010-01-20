<?php

function jok_cumbidu($socket, $channel, $sender, $msg, $infos)
{
	sendmsg($socket, "Mi son rotto!!! Adesso offro IO!!!!!", $channel);
	send($socket, "NOTICE $channel :cumbida a tutti!! :)\n");
}

?>
