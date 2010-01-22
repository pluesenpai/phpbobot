<?php

function rmop($irc, $irc_chan, $sender, $msg, $op)
{
	global $operators, $db;

	if(in_array($op[1], $operators)) {
		$db->del_operator($op[1]);
		sendmsg($irc, sprintf($translations->bot_gettext("operators-rmop-ok-%s"), $op[1]), $irc_chan); //"Fatto!!! $op[1] non &egrave; pi&ugrave; operatore!!!"
	} else
		sendmsg($irc, sprintf($translations->bot_gettext("operators-rmop-ko-%s"), $op[1]), $irc_chan); //"Spiacente... $op[1] non era un operatore!!!"
}

?>
