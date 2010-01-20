<?php

function delquote($socket, $channel, $sender, $msg, $infos)
{
	global $db;

	$quote_id = $infos[1];

	$cond_f = array("IDQuote");
	$cond_o = array("=");
	$cond_v = array($quote_id);
	$result = $db->remove("quotes", $cond_f, $cond_o, $cond_v);

	sendmsg($socket, "Quote \00301\002#{$quote_id}\002 deleted!!", $channel);
}

?>
