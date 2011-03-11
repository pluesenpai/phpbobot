<?php

	function addvar($socket, $channel, $sender, $msg, $infos)
	{
		global $db, $translations;

		if(isset($infos[3])) {
			$var = $infos[3];
			$meaning = $infos[4];
			$description = $infos[5];
		} else {
			$var = $infos[1];
			$meaning = $infos[2];
			$description = "NULL";
		}

		$known = $db->select(array("bpp"), array("var"), array(""), array("var"), array("="), array("{$var}"), 1);
		if(count($known) > 0) {
			sendmsg($socket, "{$sender} " . sprintf($translations->bot_gettext("bpp-var_exists-%s"), $var), $channel);
			return;
		}

		if(word_count($var) > 1) {
			sendmsg($socket, "{$sender} " . sprintf($translations->bot_gettext("bpp-too_many_words-%s"), $var), $channel);
			return;
		}

		$db->insert("bpp", array("var", "meaning", "description"), array($var, $meaning, $description));
		$params = array(
			"var" => $var,
			"meaning" => $meaning,
			"description" => ($description == "NULL") ? "" : $description;
		);
		callpage("vars", "add", $params);
		//getpage("http://www.lucacireddu.it/vars/engine.php?action=add&psw=bd89d1d862bd5ba278ea89184038841a&var={$infos[1]}&meaning={$infos[2]}");
		sendmsg($socket, sprintf($translations->bot_gettext("bpp-var_add_ok-%s"), $infos[1]), $channel);
	}

?>
