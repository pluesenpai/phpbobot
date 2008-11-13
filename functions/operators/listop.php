<?php

  function listop($irc, $irc_chan, $op)
  {
    global $operators;
    sendmsg($irc, "Ecco la lista degli operatori:", $irc_chan);
    $testo_p = implode(" ", $operators);
    sendmsg($irc, "$testo_p", $irc_chan);
  }

?>