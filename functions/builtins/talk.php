<?php
	function talk($socket, $channel, $sender, $msg, $infos)
	{
		global $parla, $db;

		if(!$parla[$channel]) {
			$db->update("chan", array("talk"), array("true"), array("name"), array("="), array($channel));
			$parla[$channel] = true;
			sendmsg($socket, _("talk-message"), $channel);
		}
	}
?>
