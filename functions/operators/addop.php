<?php

function addop($irc, $irc_chan, $op)
{
  file_put_contents("operators.txt", "\n$op[1]", FILE_APPEND + LOCK_EX);
  send($irc, "PRIVMSG $irc_chan :Aggiunto $op[1] come operatore!!!\n");
}

?>
