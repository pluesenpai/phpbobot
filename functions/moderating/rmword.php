<?php

  function rmword($socket, $channel, $infos)
  {
    global $bad_words;
    $ret = array_search($infos[1], $bad_words);
    if($ret != false) {
      unset($bad_words[$ret]);
      $bad_words = array_values($bad_words);
      file_put_contents("functions/moderating/bad_words.txt", implode("\n", $bad_words), LOCK_EX);
      sendmsg($socket, "Fatto!!! $infos[1] non &egrave; pi&ugrave; nella lista!!!", $channel);
    } else
      sendmsg($socket, "Spiacente... $infos[1] non era nella lista!!!", $channel);
  }

?>