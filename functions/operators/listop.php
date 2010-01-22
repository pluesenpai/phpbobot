<?php

function listop($irc, $irc_chan, $sender, $msg, $op)
{
	global $operators, $translations;

	$testo_p = implode(" ", $operators);
	sendmsg($irc, $translations->bot_gettext("operators-listop"), $irc_chan); //"Ecco la lista degli operatori:"
	sendmsg($irc, "$testo_p", $irc_chan);
}

?>