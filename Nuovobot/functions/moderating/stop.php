<?php

function stop($socket, $channel, $sender, $msg, $infos)
{
	global $db, $translations;

	$db->update("chan", array("moderated"), array("false"), array("name"), array("="), array($channel));
	sendmsg($socket, $translations->bot_gettext("moderating-stop"), $channel); //"Ora NON modero pi&ugrave; il canale!!!"
}

?>