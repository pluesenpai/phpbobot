<?php

function search($socket, $channel, $sender, $msg, $infos)
{
	global $db, $translations;

	$definitions_word = preg_replace("/\*/", "%", str_esc($infos[1]));
	$definitions_word = preg_replace("/\?/", "_", $definitions_word);
	$definitions_max = 5;

	$cond_f = array("def_name");
	$cond_o = array("LIKE");
	$cond_v = array("{$definitions_word}");

	$result = $db->select(array("definitions"), array("def_name", "def_text"), array("", ""), $cond_f, $cond_o, $cond_v, $definitions_max, "desc*def_id");

	$def_counter = 1;

	foreach($result as $definitions) {
		sendmsg($socket, sprintf($translations->bot_gettext("definitions-number-%s-%s"), $def_counter, $definitions["def_name"]), $channel); //"Definition n. \037{$def_counter}\037 for \002{$definitions["def_name"]}"
		foreach(explode("\n", $definitions["def_text"]) as $def_row)
			sendmsg($socket, "    {$def_row}", $channel);
		$def_counter++;
	}

}

?>
