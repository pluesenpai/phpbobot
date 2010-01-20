<?php

function modera($socket, $channel, $sender, $msg, $infos)
{
	global $db;

	$db->update("chan", array("moderated"), array("true"), array("name"), array("="), array($channel));
	sendmsg($socket, "Ora modero il canale!!!", $channel);
}

?>