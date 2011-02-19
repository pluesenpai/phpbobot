<?php

function delquote($socket, $channel, $sender, $msg, $infos)
{
	global $db, $translations;

	$quote_id = $infos[1];

	$cond_f = array("IDQuote");
	$cond_o = array("=");
	$cond_v = array($quote_id);
	$result = $db->remove("quotes", $cond_f, $cond_o, $cond_v);

	sendmsg($socket, sprintf($translations->bot_gettext("quotes-deleted-%s"), IRCColours::BOLD . "#{$quote_id}" . IRCColours::Z), $channel); //"Quote \00301\002#{$quote_id}\002 deleted!!"

	//getpage("http://www.lucacireddu.it/quotes/engine.php?action=del&psw=bd89d1d862bd5ba278ea89184038841a&id={$quote_id}");
}

?>
