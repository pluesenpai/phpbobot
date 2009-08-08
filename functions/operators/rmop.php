<?php

function rmop($irc, $irc_chan, $sender, $msg, $op)
{
	global $operators;
	global $db;

	if(in_array($op[1], $operators)) {
		//unset($operators[$ret]);
		//$operators = array_values($operators);
		//file_put_contents("functions/operators/operators.txt", implode("\n", $operators), LOCK_EX);
		elimina_operatore($db, $op[1]);
		sendmsg($irc, "Fatto!!! $op[1] non &egrave; pi&ugrave; operatore!!!", $irc_chan);
	} else
		sendmsg($irc, "Spiacente... $op[1] non era un operatore!!!", $irc_chan);
}

?>
