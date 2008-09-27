<?php

  function stop($socket, $channel, $infos)
  {
    $var = "0";
    file_put_contents("moderated.txt", "$var", LOCK_EX);
    send($socket, "PRIVMSG $channel :Ora NON modero più il canale!!!\n");
  }

?>