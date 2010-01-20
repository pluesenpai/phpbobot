<?php
	function paste($socket, $channel, $sender, $msg, $infos)
	{
		global $paste_enabled;
		global $dir_paste;

		if($infos[1] != "PRIVMSG")
			return;

		if(isset($paste_enabled[$sender]) && $paste_enabled[$sender]) {
			$content = file("{$dir_paste}{$sender}_paste.txt");
			$ch = str_replace(array("\n","\r"), "", $content[0]);
			echo $ch;
			if($channel == $ch) {
				if(!preg_match("/^paste/", $msg)) {
					file_put_contents("{$dir_paste}{$sender}_paste.txt", "$msg\n", FILE_APPEND + LOCK_EX);
				}
			}
		}
	}
?>