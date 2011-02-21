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
		global $irc, $functions, $translations;

		$s = $sender;
		$pid = pcntl_fork();
		if($pid == -1) {
			die("Could not fork");
		} elseif(!$pid) {
			if($folder != "" && !in_array($folder, getDirs("functions/"))) {
				sendmsg($irc, sprintf(_("help-groupnotpresent-%s-%s"), $sender, $folder), $s, 1, true); //"Spiacente $sender... Non ho nessun gruppo chiamato $folder"
				return;
			}
			if($short)
				sendmsg($irc, _("help-groupfunctions") , $s, 1, true); //"Ecco la lista dei gruppi di funzioni:"
			else
				sendmsg($irc, _("help-functions"), $s, 1, true); //"Ecco la lista delle funzioni:"

			$old = "";
			foreach($functions as $func) {
				if($func["folder"] != $old) {
					$old = $func["folder"];
					$group_name = $translations->bot_gettext("{$old}-group_name");
					$group_descr = $translations->bot_gettext("{$old}-group_descr");
					if($folder == "" || ($folder != "" && $old == $folder))
						sendmsg($irc, IRCColours::BOLD . IRCColours::BLUE . "$old ($group_name)" . IRCColours::Z . ":: $group_descr", $s, .5, true);
				}
				if($folder != "" && $old != $folder) {
					continue;
				}
				if(!$short) {
					$priv = " ";
					if($func["privileged"] == 1)
						$priv = "*";
					sendmsg($irc, "\t\t($priv) " . IRCColours::UNDERLINE . IRCColours::RED . $func["descr_name"] . IRCColours::Z . ": " . $func["descr"], $s, .5, true);
				}
			}

			if(!$short)
				sendmsg($irc, "     " . _("help-botop_needed"), $s); //NOTE: (*) means that you need to be bot operator to exec it.
			posix_kill(posix_getpid(), 9);
		}
	}
?>