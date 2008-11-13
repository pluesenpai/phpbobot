<?php

  function modera($socket, $channel, $infos)
  {
    $var = "1";
    file_put_contents("functions/moderating/moderated.txt", "$var", LOCK_EX);
    sendmsg($socket, "Ora modero il canale!!!", $channel);
  }

?>