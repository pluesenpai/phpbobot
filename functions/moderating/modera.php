<?php

function modera($socket, $channel, $sender, $msg, $infos)
{
	global $db, $translations;

	$db->update("chan", array("moderated"), array("true"), array("name"), array("="), array($channel));
	sendmsg($socket, $translations->bot_gettext("moderating-started"), $channel); //"Ora modero il canale!!!"
}

?>