<?php
	function talkall($socket, $channel, $sender, $msg, $infos)
	{
		global $parla, $irc_chans;

		away($socket, "", false);
		foreach($irc_chans as $index => $value) {
			$parla[$value] = true;
			sendmsg($socket, _("back-message"), $value);
		}
	}
?>
