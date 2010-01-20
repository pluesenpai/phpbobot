<?php

function info($socket, $channel, $sender, $msg, $infos)
{
	global $moderated;

	if($moderated[$channel])
		sendmsg($socket, "Sto moderando il canale!!!", $channel);
	else
		sendmsg($socket, "NON sto moderando il canale!!!", $channel);
}

?>