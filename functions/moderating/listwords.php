<?php

  function listwords($socket, $channel, $infos)
  {
    global $bad_words;
    send($socket, "PRIVMSG $channel :Allora... Ti do l'elenco delle parole vietate!! ;)\n");
    $testo_p = implode(" ", $bad_words);
    send($socket, "PRIVMSG $channel :$testo_p\n");
  }

?>