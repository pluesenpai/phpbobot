<?php

function birthday($socket, $channel, $sender, $msg, $infos)
{
	global $db;

	$cond_f = array("username");
	$cond_o = array("=");
	$cond_v = array($sender);

	$result = $db->select(array("user"), array("birthday"), array(""), $cond_f, $cond_o, $cond_v);

	if(isset($result[0]) && count($result[0]) > 0) {
		if($result[0]["birthday"] > date("Y-m-d"))
			return;
		elseif($result[0]["birthday"] == date("Y-m-d")) {
			sendmsg($socket, sprintf($translations->bot_gettext("birthday-theday-%s"), $sender), $channel); //"$sender!! Ma... Oggi &egrave; il tuo compleanno!!! Auguri!!!!"

			$year = ((int)date('Y')) + 1;
			$month = date('n');
			$day = date('j');
			$hour = 0;
			$minute = 0;
			$second = 0;
			$data = date("Y-m-d", mktime($second, $minute, $hour, $month, $day, $year));

			$db->update("user", array("birthday"), array($data), $cond_f, $cond_o, $cond_v);
		} elseif($result[0]["birthday"] != "" && $result[0]["birthday"] < date("Y-m-d")) {
			$year = ((int)date('Y')) + 1;

			list(, $month, $day) = explode("-", $result[0]["birthday"]);

// 			$month = date('n');
// 			$day = date('j');
			$hour = 0;
			$minute = 0;
			$second = 0;
			$data = date("Y-m-d", mktime($second, $minute, $hour, $month, $day, $year));
		}
	}
}

?>
