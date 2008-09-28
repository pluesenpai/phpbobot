<?php

  function modera($socket, $channel, $infos)
  {
    $var = "1";
    file_put_contents("functions/moderating/moderated.txt", "$var", LOCK_EX);
    send($socket, "PRIVMSG $channel :Ora modero il canale!!!\n");
  }

?>