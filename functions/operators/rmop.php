<?php

  function rmop($irc, $irc_chan, $op)
  {
    global $operators;
    $ret = array_search($op[1], $operators);
    if($ret != false) {
      unset($operators[$ret]);
      $operators = array_values($operators);
      file_put_contents("functions/operators/operators.txt", implode("\n", $operators), LOCK_EX);
      send($irc, "PRIVMSG $irc_chan :Fatto!!! $op[1] non è più operatore!!!\n");
    } else
      send($irc, "PRIVMSG $irc_chan :Spiacente... $op[1] non era un operatore!!!\n");
  }

?>
