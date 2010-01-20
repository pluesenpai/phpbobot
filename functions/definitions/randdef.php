<?php

function randdef($socket, $channel, $sender, $msg, $infos)
{
	global $db;

	$cond_f = array();
	$cond_o = array();
	$cond_v = array();

	$result = $db->select(array("definitions"), array("def_name", "def_text"), array("", ""), $cond_f, $cond_o, $cond_v, "random", 1);

	$def_ch = $result[0];

	sendmsg($socket, "\037Random definition for {$sender}!!!\037 Chosen definition: \002{$def_ch["def_name"]}", $channel);
	foreach(explode("\n", $def_ch["def_text"]) as $def_row)
		sendmsg($socket, "    {$def_row}", $channel);
}

?>
