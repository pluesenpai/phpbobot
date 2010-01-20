<?php

function inviteto($socket, $channel, $sender, $msg, $infos)
{
	send($socket, "NOTICE $channel :offre un {$infos[1]} a {$infos[2]} :)\n");
}

?>