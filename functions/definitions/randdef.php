<?php

function randdef($socket, $channel, $sender, $msg, $infos)
{
	global $db, $translations;

	$cond_f = array();
	$cond_o = array();
	$cond_v = array();

	$result = $db->select(array("definitions"), array("def_name", "def_text"), array("", ""), $cond_f, $cond_o, $cond_v, 1, "random");

	$def_ch = $result[0];

	sendmsg($socket, sprintf($translations->bot_gettext("definitions-chosen-%s-%s"), $sender, $def_ch["def_name"]), $channel); //"\037Random definition for {$sender}!!!\037 Chosen definition: \002{$def_ch["def_name"]}"
	foreach(explode("\n", $def_ch["def_text"]) as $def_row)
		sendmsg($socket, "    {$def_row}", $channel);
}

?>
