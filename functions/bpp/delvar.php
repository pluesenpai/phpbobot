<?php

	function delvar($socket, $channel, $sender, $msg, $infos)
	{
		global $db, $translations;

		$known = $db->select(array("bpp"), array("var"), array(""), array("var"), array("="), array("{$infos[1]}"), 1);
		if(count($known) <= 0) {
			sendmsg($socket, "{$sender} " . sprintf($translations->bot_gettext("bpp-var_notexists-%s"), $infos[1]), $channel);
			return;
		}

		sendmsg($socket, sprintf($translations->bot_gettext("bpp-var_remove_ok-%s"), $infos[1]), $channel);
		$db->remove("bpp", array("var"), array("="), array("{$infos[1]}"));
		$params = array("var" => $infos[1]);
		callpage("vars", "del", $params);
		//getpage("http://www.lucacireddu.it/vars/engine.php?action=del&psw=bd89d1d862bd5ba278ea89184038841a&var={$infos[1]}");
	}

?>
