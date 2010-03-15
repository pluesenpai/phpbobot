<?php

function ansa($socket, $channel, $sender, $msg, $infos)
{
// 	global $translations;
	sendmsg($socket, ".ansa {$infos[1]}", "#bottoli");
}

?>
