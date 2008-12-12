<?php

function addop($irc, $irc_chan, $sender, $msg, $op)
{
	file_put_contents("functions/operators/operators.txt", "\n$op[1]", FILE_APPEND + LOCK_EX);
	sendmsg($irc, "Aggiunto $op[1] come operatore!!!", $irc_chan);
}

?>
