<?php

function quote($socket, $channel, $sender, $msg, $infos)
{
	global $db;

	$quote_id = (int)$infos[1];

	$cond_f = array("IDQuote", "poster", "sender", "channel");
	$cond_o = array("=", "=", "=", "=");
	$cond_v = array($quote_id, "!U1.IDUser!", "!U2.IDUser!", "IDChan");

	$result = $db->select(array("quotes", "user U1", "user U2", "chan"), array("IDQuote", "message", "U1.username", "U2.username", "name"), array("", "", "the_poster", "the_sender", "the_chan"), $cond_f, $cond_o, $cond_v);

	if(count($result) > 0) {
		foreach($result as $quote) {
			sendmsg($socket, "\00301\002#{$quote["IDQuote"]}\002 \037(quoted by {$quote["the_sender"]})\037: \00311<{$quote["the_poster"]}>\00301 \017" . toUTF8(stripslashes($quote["message"])), $channel);
		}
	} else
		sendmsg($socket, "Spiacente... Non esiste la quote n. $quote_id.", $channel);

}

?>
