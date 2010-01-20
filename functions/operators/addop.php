<?php

function addop($irc, $irc_chan, $sender, $msg, $op)
{
	global $db;

	$db->set_operator($op[1]);
	sendmsg($irc, "Aggiunto $op[1] come operatore!!!", $irc_chan);
}

?>
