<?php
	function pastetypes($socket, $channel, $sender, $msg, $infos)
	{
		global $paste_langs1;
		sendmsg($socket, "Ecco i linguaggi riconosciuti da http://rafb.net/paste/:", $channel, 0, true);
		sendmsg($socket, implode(", ", $paste_langs1), $channel, 0, true);
		sendmsg($socket, "Attenzione! Uso un riconoscimento Case Sensitive,", $channel, 0, true);
		sendmsg($socket, "quindi se non scrivi il linguaggio correttamente userò `Text`", $channel, 0, true);
	}
?>