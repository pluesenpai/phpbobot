<?php

  function addword($socket, $channel, $infos)
  {
    file_put_contents("functions/moderating/bad_words.txt", "\n$infos[1]", FILE_APPEND + LOCK_EX);
    //array_push($bad_words, $infos[1]);
    send($socket, "PRIVMSG $channel :Aggiunta la parola $infos[1] nella lista!!!\n");
  }

?>