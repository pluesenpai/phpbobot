<?php

function jok_coffeeto($socket, $channel, $sender, $msg, $infos)
{
	send($socket, "NOTICE $channel :offre un coffee a ${infos[1]} da parte di $sender :)\n");
}

?>
