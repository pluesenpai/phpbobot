<?php

function addop($irc, $irc_chan, $sender, $msg, $op)
{
	global $db, $translations;

	$db->set_operator($op[1]);
	sendmsg($irc, sprintf($translations->bot_gettext("operators-addop-ok-%s"), $op[1]), $irc_chan); //"Aggiunto $op[1] come operatore!!!"
}

?>
