<?php

function listop($irc, $irc_chan, $sender, $msg, $op)
{
	global $operators;

	$testo_p = implode(" ", $operators);
	sendmsg($irc, "Ecco la lista degli operatori:", $irc_chan);
	sendmsg($irc, "$testo_p", $irc_chan);
}

?>