<?php

	function bpp($socket, $channel, $sender, $msg, $infos)
	{
		global $db, $translations, $is_bpp_on;

		if(!preg_match("/\\$/", $msg)) {
			return;
		}

		$always = preg_match("/always/", $infos[0]);
		if($always == false) {
			$n_spaces = substr_count($msg, " ");
			$msg_arr = explode(" ", $msg, $n_spaces + 1);
			$msg = $msg_arr[$n_spaces];
		}
		if($always == true && ($is_bpp_on[$channel] == false || $infos[1] != "PRIVMSG")) {
			return;
		}

		if(!preg_match_all("/\\$([a-zA-Z_][a-zA-Z0-9_]*)/", $msg, $variables)) {
			return;
		}

		$vars = $variables[1];
		sort($vars);

		$wrong = $db->select(array("bpp"), array("var", "meaning", "description"), array("", "", ""), array("var"), array("IN"), array("('" . implode("', '", $vars) . "')"), 0, "asc*var");
		$known = $db->select(array("bpp"), array("var"), array(""), array("var"), array("IN"), array("('" . implode("', '", $vars) . "')"), 0, "asc*var");

		if(count($wrong) > 0) {
			for($i = 0; $i < count($wrong); $i++) {
				if($always == false) {
					$msg = preg_replace("/\\\${$wrong[$i]["var"]}/", "{$wrong[$i]["meaning"]} ({$wrong[$i]["description"]})", $msg);
				} else {
					$msg = preg_replace("/\\\${$wrong[$i]["var"]}/", $wrong[$i]["meaning"], $msg);
				}
			}

			if($always == false) {
				sendmsg($socket, "{$sender} " . $translations->bot_gettext("bpp-means") . ": {$msg}", $channel);
			} else {
				sendmsg($socket, $msg, $channel);
			}
		}

		if(count($wrong) == 0 || (count($vars) - count($wrong)) > 0) {
			if(count($known) == 0) {
				$known[] = array();
			}
			sendmsg($socket, $translations->bot_gettext("bpp-dontknow") . ": $" . implode(" $", array_diff($vars, $known[0])), $channel);
		}
	}

?>
