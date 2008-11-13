<?php

  function info($socket, $channel, $infos)
  {
    global $moderated;
    if($moderated)
      sendmsg($socket, "Sto moderando il canale!!!", $channel);
    else
      sendmsg($socket, "NON sto moderando il canale!!!", $channel);
  }

?>