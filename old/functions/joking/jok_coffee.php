<?php

function jok_coffee($socket, $channel, $sender, $msg, $infos)
{
	send($socket, "NOTICE $channel :offre un coffee a $sender :)\n");
}

?>
