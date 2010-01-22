<?php

	function startpaste($socket, $channel, $sender, $msg, $infos)
	{
		global $translations;

		if(isset($paste_enabled[$sender]) && $paste_enabled[$sender]) {
			sendmsg($socket, sprintf($translations->bot_gettext("paste-already_open-%s"), $sender), $channel); //"$sender:: Hai gi&agrave; un paste aperto. Digita `pasted` prima di avviarne uno nuovo."
		} else {
			global $user_name, $db, $dir_paste, $paste_langs1;

			if(!file_exists($dir_paste))
				mkdir($dir_paste, 0755, true);

			if(!in_array($infos[1], $paste_langs1))
				$lang = "Text";
			else
				$lang = $infos[1];

			$fp = fopen("{$dir_paste}{$sender}_paste.txt", "w");
			fprintf($fp, "%s\n%s\n%s\n", $channel, $lang, $infos[2]);
			fclose($fp);

			$db->update("user", array("paste_enabled"), array("true"), array("username"), array("="), array($sender));

			sendmsg($socket, sprintf($translations->bot_gettext("paste-newpaste-%s"), $sender), $channel); //"$sender:: Per chiudere il paste e inviarlo, digita `pasted`"
		}
	}

?>