<?php

  function listop($irc, $irc_chan, $op)
  {
    global $operators;
    send($irc, "PRIVMSG $irc_chan :Ecco la lista degli operatori:\n");
    $testo_p = implode(" ", $operators);
    send($irc, "PRIVMSG $irc_chan :$testo_p\n");
  }

?>