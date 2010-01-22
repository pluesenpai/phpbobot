<?php
	function pastetypes($socket, $channel, $sender, $msg, $infos)
	{
		global $paste_langs1, $translations;

		sendmsg($socket, sprintf($translations->bot_gettext("paste-languages-%s"), "http://rafb.net/paste/"), $channel, 0, true); //"Ecco i linguaggi riconosciuti da http://rafb.net/paste/:"
		sendmsg($socket, implode(", ", $paste_langs1), $channel, 0, true);
		sendmsg($socket, $translations->bot_gettext("paste-case_sensitive_1"), $channel, 0, true); //"Attenzione! Uso un riconoscimento Case Sensitive,"
		sendmsg($socket, $translations->bot_gettext("paste-case_sensitive_2"), $channel, 0, true); //"quindi se non scrivi il linguaggio correttamente user&ograve; `Text`"
	}
?>