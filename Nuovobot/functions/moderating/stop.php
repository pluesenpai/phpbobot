<?php

function stop($socket, $channel, $sender, $msg, $infos)
{
	global $db;

	$db->update("chan", array("moderated"), array("false"), array("name"), array("="), array($channel));
	sendmsg($socket, "Ora NON modero pi&ugrave; il canale!!!", $channel);
}

?>