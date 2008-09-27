<?php

  function rmword($socket, $channel, $infos)
  {
    global $bad_words;
    $ret = array_search($infos[1], $bad_words);
    if($ret != false) {
      unset($bad_words[$ret]);
      $bad_words = array_values($bad_words);
      file_put_contents("bad_words.txt", implode("\n", $bad_words), LOCK_EX);
      send($socket, "PRIVMSG $channel :Fatto!!! $infos[1] non è più nella lista!!!\n");
    } else
      send($socket, "PRIVMSG $channel :Spiacente... $infos[1] non era nella lista!!!\n");
  }

?>