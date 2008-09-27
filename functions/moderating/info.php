<?php

  function info($socket, $channel, $infos)
  {
    global $moderated;
    if($moderated)
      send($socket, "PRIVMSG $channel :Sto moderando il canale!!!\n");
    else
      send($socket, "PRIVMSG $channel :NON sto moderando il canale!!!\n");
  }

?>