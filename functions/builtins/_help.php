<?php
	/**
	  * Prints help of the bot on a private message
	  * @param $irc Socket
	  * @param $sender User to send message to
	  * @param $functions List of functions
	  * @param $short If set to true, shows a summary of the help
	  * @param $folder If setted prints only commands on that specified folder
	  */
	function _help($sender, $short = true, $folder = "")
	{
		global $irc, $functions;

		$s = $sender;
		$pid = pcntl_fork();
		if($pid == -1) {
			die("Could not fork");
		} elseif(!$pid) {
			if($folder != "" && !in_array($folder, getDirs("functions/"))) {
				sendmsg($irc, "Spiacente $sender... Non ho nessun gruppo chiamato $folder", $s, 1, true);
				return;
			}
			if($short)
				sendmsg($irc, "Ecco la lista dei gruppi di funzioni:", $s, 1, true);
			else
				sendmsg($irc, "Ecco la lista delle funzioni:", $s, 1, true);

			$old = "";
			foreach($functions as $func) {
				if($func["folder"] != $old) {
					$old = $func["folder"];
					if($folder == "" || ($folder != "" && $old == $folder))
						sendmsg($irc, "$old::", $s, .5, true);
					//if($short)
						//sendmsg($irc, "$group_descr", $s, .5, true);
				}
				if($folder != "" && $old != $folder) {
					continue;
				}
				if(!$short) {
					$priv = " ";
					if($func["privileged"] == 1)
						$priv = "*";
					sendmsg($irc, "\t\t($priv) {$func["descr_name"]}: {$func["descr"]}", $s, .5, true);
				}
			}

			if(!$short)
				sendmsg($irc, "     NOTE: (*) means that you need to be bot operator to exec it.", $s);
			posix_kill(posix_getpid(), 9);
		}
	}
?>