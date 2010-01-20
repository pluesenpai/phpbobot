<?php

function modera($socket, $channel, $sender, $msg, $infos)
{
	global $db;
// 	$var = "1";
// 	file_put_contents("functions/moderating/moderated.txt", "$var", LOCK_EX);
	$db->update("chan", array("moderated"), array("'true'"), array("name"), array("="), array("'$channel'"));
	sendmsg($socket, "Ora modero il canale!!!", $channel);
}

?>