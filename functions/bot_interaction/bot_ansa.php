<?php

function bot_ansa($socket, $channel, $sender, $msg, $infos)
{
// 	global $translations;
	if($infos[1] != "PRIVMSG" || $channel != "#bottoli")
		return;

	if(preg_match("/ANSA|^ /", $msg) && $sender == "Bender")
		sendmsg($socket, $msg, "#sardylan");
}

?>
