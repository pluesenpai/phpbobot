<?php

function rmop($irc, $irc_chan, $sender, $msg, $op)
{
	global $operators;
	$ret = array_search($op[1], $operators);
	if($ret != false) {
		unset($operators[$ret]);
		$operators = array_values($operators);
		file_put_contents("functions/operators/operators.txt", implode("\n", $operators), LOCK_EX);
		sendmsg($irc, "Fatto!!! $op[1] non &egrave; pi&ugrave; operatore!!!", $irc_chan);
	} else
		sendmsg($irc, "Spiacente... $op[1] non era un operatore!!!", $irc_chan);
}

?>
