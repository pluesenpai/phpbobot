<?php

//select count(IDQuote),poster from quotes group by poster limit 3;           MOSTQUOTED
//select count(IDQuote),sender from quotes group by sender limit 3;           MOSTQUOTER

function mostquoter($socket, $channel, $sender, $msg, $infos)
{
	global $db, $translations;

	$cond_f = array("sender", "channel", "name");
	$cond_o = array("=", "=", "=");
	$cond_v = array("IDUser", "IDChan", $channel);

	//select count(IDQuote) as totale, username as the_sender, name as the_chan
	//from quotes, user, chan where sender=IDUser and channel=IDChan and name="#sardylan"
	//group by the_sender
	//order by totale desc
	//limit 3;

	$result = $db->select(array("quotes", "user", "chan"), array("count(IDQuote)", "username", "name"), array("totale", "the_sender", "the_chan"), $cond_f, $cond_o, $cond_v, 3, "desc*totale", "group the_sender");

	print_r($result);

	if(count($result) > 0) {
		$cont = 1;
		foreach($result as $quote) {
			sendmsg($socket, sprintf($translations->bot_gettext("quotes-mostquoter-message-%s-%s-%s"), IRCColours::BOLD . "{$cont}°" . IRCColours::Z, IRCColours::AQUA . "{$quote["the_sender"]}" . IRCColours::Z, IRCColours::RED . "{$quote["totale"]}" . IRCColours::Z), $channel, 0, true); //Al \002{$cont}°\002 posto... \00311{$result["the_sender"]}\00301 ha quotato \00304{$result["totale"]}\00301 volte!
			//sendmsg($socket, "\00301\002#{$quote["IDQuote"]}\002 \037(quoted by {$quote["the_sender"]})\037: \00311<{$quote["the_poster"]}>\00301 \017" . toUTF8(stripslashes($quote["message"])), $channel);
			$cont++;
		}
	} else
		sendmsg($socket, $translations->bot_gettext("quotes-stats-notfound"), $channel); //"Nessuna quote disponibile."
}

?>
