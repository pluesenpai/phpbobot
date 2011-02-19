<?php

function quote($socket, $channel, $sender, $msg, $infos)
{
	global $db, $translations;

	$quote_id = (int)$infos[1];

	$cond_f = array("IDQuote", "poster", "sender", "channel");
	$cond_o = array("=", "=", "=", "=");
	$cond_v = array($quote_id, "!U1.IDUser!", "!U2.IDUser!", "IDChan");

	$result = $db->select(array("quotes", "user U1", "user U2", "chan"), array("IDQuote", "message", "U1.username", "U2.username", "name"), array("", "", "the_poster", "the_sender", "the_chan"), $cond_f, $cond_o, $cond_v);

	if(count($result) > 0) {
		foreach($result as $quote) {
			sendmsg($socket, IRCColours::BOLD . "#" . $quote["IDQuote"] . IRCColours::Z . " " . IRCColours::UNDERLINE . "(quoted by {$quote["the_sender"]})" . IRCColours::Z . ": " . IRCColours::AQUA . "<{$quote["the_poster"]}>" . IRCColours::Z . " " . toUTF8(stripslashes($quote["message"])), $channel);
		}
	} else
		sendmsg($socket, sprintf($translations->bot_gettext("quotes-notexists-%s"), $quote_id), $channel); //"Spiacente... Non esiste la quote n. $quote_id."

}

?>
