<?php

  function stop($socket, $channel, $infos)
  {
    $var = "0";
    file_put_contents("functions/moderating/moderated.txt", "$var", LOCK_EX);
    sendmsg($socket, "Ora NON modero pi&ugrave; il canale!!!", $channel);
  }

?>