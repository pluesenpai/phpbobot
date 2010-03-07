<?php

function findquotes($socket, $channel, $sender, $msg, $infos)
{
	global $db, $translations;

	$user_num = count($infos);
	$max = 5;
	while($max * $user_num > 10)
		$max--;

	for($i = 1; ($i < $user_num) && ($i < 10); $i++) {
		$quote_text = str_esc($infos[$i]);

		$cond_f = array("message", "poster", "sender", "channel");
		$cond_o = array("LIKE", "=", "=", "=");
		$cond_v = array("%{$quote_text}%", "!U1.IDUser!", "!U2.IDUser!", "IDChan");

		$result = $db->select(array("quotes", "user U1", "user U2", "chan"), array("IDQuote", "message", "U1.username", "U2.username", "name"), array("", "", "the_poster", "the_sender", "the_chan"), $cond_f, $cond_o, $cond_v, $max, "");

		if(count($result) > 0) {
			foreach($result as $quote)
				sendmsg($socket, "\00301\002#{$quote["IDQuote"]}\002 \037(quoted by {$quote["the_sender"]})\037: \00311<{$quote["the_poster"]}>\00301 \017" . toUTF8(stripslashes($quote["message"])), $channel);
		} else
			sendmsg($socket, $translations->bot_gettext("quotes-findquotes-notfound"), $channel); //"Nessuna quote disponibile contenente il testo cercato."
	}

}

?>
