<?php

function lastquote($socket, $channel, $sender, $msg, $infos)
{
	global $db;

	$cond_f = array("poster", "sender", "channel", "name");
	$cond_o = array("=", "=", "=", "=");
	$cond_v = array("!U1.IDUser!", "!U2.IDUser!", "IDChan", $channel);

	$result = $db->select(array("quotes", "user U1", "user U2", "chan"), array("IDQuote", "message", "U1.username", "U2.username", "name"), array("", "", "the_poster", "the_sender", "the_chan"), $cond_f, $cond_o, $cond_v);

	$quote = end($result);

	sendmsg($socket, "\00301\002#{$quote["IDQuote"]}\002 \037(quoted by {$quote["the_sender"]})\037: \00311<{$quote["the_poster"]}>\00301 \017" . toUTF8(stripslashes($quote["message"])), $channel);
}

?>
