<?php

function userquotes($socket, $channel, $sender, $msg, $infos)
{
	global $db, $translations;

	$user_num = count($infos);
	$max = 5;
	while($max * $user_num > 10)
		$max--;

	for($i = 1; ($i < $user_num) && ($i < 10); $i++) {
		$quote_user = str_esc($infos[$i]);

		$cond_f = array("the_poster", "poster", "sender", "channel", "name");
		$cond_o = array("=", "=", "=", "=", "=");
		$cond_v = array($quote_user, "!U1.IDUser!", "!U2.IDUser!", "IDChan", $channel);

		$result = $db->select(array("quotes", "user U1", "user U2", "chan"), array("IDQuote", "message", "U1.username", "U2.username", "name"), array("", "", "the_poster", "the_sender", "the_chan"), $cond_f, $cond_o, $cond_v, $max, "");

		if(count($result) > 0) {
			foreach($result as $quote)
				sendmsg($socket, IRCColours::BOLD . "#"  . $quote["IDQuote"] . IRCColours::Z . " " . IRCColours::UNDERLINE . "(quoted by {$quote["the_sender"]})" . IRCColours::Z . ": " . IRCColours::AQUA . "<{$quote["the_poster"]}>" . IRCColours::Z . " " . toUTF8(stripslashes($quote["message"])), $channel);
		} else
			sendmsg($socket, sprintf($translations->bot_gettext("quotes-userquotes-notfound-%s"), $quote_user), $channel); //"Nessuna quote disponibile per l'utente $quote_user."
	}

}

?>
