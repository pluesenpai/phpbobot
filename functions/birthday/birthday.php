<?php

function birthday($socket, $channel, $sender, $msg, $infos)
{
	global $db;

	$cond_f = array("birthday", "username");
	$cond_o = array("=", "=");
	$cond_v = array("DATE('now')", "'$sender'");

	$result = $db->select(array("user"), array("birthday"), array(""), $cond_f, $cond_o, $cond_v);

	if(count($result) > 0) {
		sendmsg($socket, "$sender!! Ma... Oggi &egrave; il tuo compleanno!!! Auguri!!!!", $channel);
		$year = ((int)date('Y')) + 1;
		$month = date('n');
		$day = date('j');
		$hour = 0;
		$minute = 0;
		$second = 0;
		$data = date("Y-m-d", mktime($second, $minute, $hour, $month, $day, $year));

		$db->update("user", array("birthday"), array("'$data'"), $cond_f, $cond_o, $cond_v);
	}
}

?>
