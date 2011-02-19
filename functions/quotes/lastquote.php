<?php

function lastquote($socket, $channel, $sender, $msg, $infos)
{
	global $db, $translations;

	$cond_f = array("poster", "sender", "channel", "name");
	$cond_o = array("=", "=", "=", "=");
	$cond_v = array("!U1.IDUser!", "!U2.IDUser!", "IDChan", $channel);

	$result = $db->select(array("quotes", "user U1", "user U2", "chan"), array("IDQuote", "message", "U1.username", "U2.username", "name"), array("", "", "the_poster", "the_sender", "the_chan"), $cond_f, $cond_o, $cond_v);

	if(count($result) > 0) {
		$quote = end($result);
		sendmsg($socket, IRCColours::BOLD . "#{$quote["IDQuote"]}" . IRCColours::Z . " " . IRCColours::UNDERLINE . "(quoted by {$quote["the_sender"]})" . IRCColours::Z . ": " . IRCColours::AQUA . "<{$quote["the_poster"]}>" . IRCColours::Z . " " . toUTF8(stripslashes($quote["message"])), $channel);
	} else
		sendmsg($socket, $translations->bot_gettext("quotes-stats-notfound"), $channel);
}

?>
