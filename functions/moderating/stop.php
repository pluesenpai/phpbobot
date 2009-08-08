<?php

function stop($socket, $channel, $sender, $msg, $infos)
{
	global $db;
// 	$var = "0";
// 	file_put_contents("functions/moderating/moderated.txt", "$var", LOCK_EX);
	$db->update("chan", array("moderated"), array("'false'"), array("name"), array("="), array("'$channel'"));
	sendmsg($socket, "Ora NON modero pi&ugrave; il canale!!!", $channel);
}

?>