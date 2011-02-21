<?php

	function addvar($socket, $channel, $sender, $msg, $infos)
	{
		global $db, $translations;

		$known = $db->select(array("bpp"), array("var"), array(""), array("var"), array("="), array("{$infos[1]}"), 1);
		if(count($known) > 0) {
			sendmsg($socket, "{$sender} " . sprintf($translations->bot_gettext("bpp-var_exists-%s"), $infos[1]), $channel);
			return;
		}

		$db->insert("bpp", array("var", "meaning"), array($infos[1], $infos[2]));
		$params = array(
			"var" => $infos[1],
			"meaning" => $infos[2]
		);
		callpage("vars", "add", $params);
		//getpage("http://www.lucacireddu.it/vars/engine.php?action=add&psw=bd89d1d862bd5ba278ea89184038841a&var={$infos[1]}&meaning={$infos[2]}");
		sendmsg($socket, sprintf($translations->bot_gettext("bpp-var_add_ok-%s"), $infos[1]), $channel);
	}

?>
