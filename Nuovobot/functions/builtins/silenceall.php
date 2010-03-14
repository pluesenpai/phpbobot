<?php
	function silenceall($socket, $channel, $sender, $msg, $infos)
	{
		global $parla, $irc_chans;

		foreach($irc_chans as $index => $value) {
			sendmsg($socket, _("away-message"), $value);
			$parla[$value] = false;
		}
		away($socket, _("away-message"));
	}
?>
