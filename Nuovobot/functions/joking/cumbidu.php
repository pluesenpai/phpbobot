<?php

function cumbidu($socket, $channel, $sender, $msg, $infos)
{
	sendmsg($socket, "Mi son rotto!!! Adesso offro IO!!!!!", $channel);
	send($socket, "NOTICE $channel :cumbida a tutti!! :)\n");
}

?>
