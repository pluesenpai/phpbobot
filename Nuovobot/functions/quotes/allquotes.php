<?php

function allquotes($socket, $channel, $sender, $msg, $infos)
{
	global $db, $myipaddress, $translations;

	$filename = "tmp/quotes.txt";
	//$ipaddress = "2537750016";
	if($myipaddress == "localhost")
		$ipaddress = "2130706433"; //127.0.0.1
	else
		$ipaddress = ip2long($myipaddress);

	$port = "30000";

	if(!file_exists($filename)) {
		file_put_contents($filename, "", LOCK_EX);

		$cond_f = array("poster", "sender", "channel");
		$cond_o = array("=", "=", "=");
		$cond_v = array("!U1.IDUser!", "!U2.IDUser!", "IDChan");

		$result = $db->select(array("quotes", "user U1", "user U2", "chan"), array("IDQuote", "message", "U1.username", "U2.username", "name"), array("", "", "the_poster", "the_sender", "the_chan"), $cond_f, $cond_o, $cond_v);

		foreach($result as $quote)
			file_put_contents($filename, "#{$quote["IDQuote"]} (quoted by {$quote["the_sender"]}):\n\t<{$quote["the_poster"]}>" . toUTF8(stripslashes($quote["message"])) . "\n\n", FILE_APPEND);
			//sendmsg($socket, "\00301\002#{$quote["IDQuote"]}\002 \037(quoted by {$quote["the_sender"]})\037: \00311<{$quote["the_poster"]}>\00301 \017" . toUTF8(stripslashes($quote["message"])), $sender, 0, true);

		$filesize = filesize($filename);

		sendmsg($socket, "\001DCC SEND {$filename} {$ipaddress} {$port} {$filesize}\001", $sender, 0, true);

		$sending = socket_create(AF_INET, SOCK_STREAM, SOL_TCP);
		if($sending) {
			//echo _("done") . "\n";
			//sckdbg($sck_debug, _("socket-partyline-listening") . " $party_addr:$party_port");
			if (!socket_set_option($sending, SOL_SOCKET, SO_REUSEADDR, 1)) {
				die(socket_strerror(socket_last_error()) . " (" . socket_last_error() . ")");
			}
			socket_bind($sending, "0.0.0.0", $port);
			socket_listen($sending);

			$conn = false;
			switch(@socket_select(array($sending), array($sending), array($sending), 60)) {
				case 2:
					echo "Connection refused\n";
					break;
				case 1:
					echo "Connection accepted\n";
					$conn = @socket_accept($sending);
					$conn = true;
					break;
				case 0:
					echo "Connection timed out\n";
					break;
			}
			if ($conn !== false) {
				//$sck = socket_accept($sending);
				socket_getpeername($sck, $remoteaddress);

				$fp = fopen($filename, "rb");
				do {
					$contents = fread($fp, "1024");
					socket_write($sck, $contents, strlen($contents));
				} while(!feof($fp));
				fclose($fp);

				socket_shutdown($sck, 2);
				sleep(1);
				socket_close($sck);
				socket_shutdown($sending, 2);
				sleep(1);
				socket_close($sending);
			}
		} else {
			echo _("error") . "\n";
			die(socket_strerror(socket_last_error()) . " (" . socket_last_error() . ")");
		}

		unlink($filename);
	} else
		sendmsg($socket, sprintf($translations->bot_gettext("quotes-already_sending-%s"), ), $channel, 0, true); //"Spiacente $sender... Sto gi&agrave; inviando un file. Riprova fra un po'..."
}

?>
