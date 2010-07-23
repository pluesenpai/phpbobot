<?php
	function silence($socket, $channel, $sender, $msg, $infos)
	{
		global $parla, $db;

		if($parla[$channel]) {
			sendmsg($socket, "...", $channel);
			$db->update("chan", array("talk"), array("false"), array("name"), array("="), array($channel));
			//$parla[$channel] = false;
		}
	}
?>
