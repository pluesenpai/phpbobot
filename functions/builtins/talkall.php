<?php
	function talkall($socket, $channel, $sender, $msg, $infos)
	{
		global $parla, $irc_chans, $db;

		away($socket, "", false);
		foreach($irc_chans as $index => $value) {
			$db->update("chan", array("talk"), array("true"), array("name"), array("="), array($value));
			//$parla[$value] = true;
			sendmsg($socket, _("back-message"), $value);
		}
	}
?>
