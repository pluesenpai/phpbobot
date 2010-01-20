<?php

function invite($socket, $channel, $sender, $msg, $infos)
{
	send($socket, "NOTICE $channel :offre un {$infos[1]} a $sender :)\n");
}

?>