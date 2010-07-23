<?php
	function silenceall($socket, $channel, $sender, $msg, $infos)
	{
		global $parla, $irc_chans, $db;

		foreach($irc_chans as $index => $value) {
			sendmsg($socket, _("away-message"), $value);
			$db->update("chan", array("talk"), array("false"), array("name"), array("="), array($value));
			//$parla[$value] = false;
		}
		away($socket, _("away-message"));
	}
?>
