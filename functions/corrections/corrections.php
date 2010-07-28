<?php
	function corrections($socket, $channel, $sender, $msg, $infos)
	{
		global $db, $translations;
	
		if($infos[1] != "PRIVMSG")
			return;

		$iduser = $db->check_user($sender);
		$idchan = $db->check_chan($channel);
		$wrong = $db->select(array("corrections"), array("last_said"), array(""), array("user_IDUser", "chan_IDChan"), array("=",  "="), array($iduser, $idchan));

		if(substr_count($msg, "/") != 3) {
			if(preg_match("#^s(\*(.+?))?/(.+)/(.*)[^/]$#", $msg)) {
				sendmsg($socket, sprintf($translations->bot_gettext("corrections-final_slash-%s"), $sender), $channel); //"Fai pi&ugrave; attenzione {$sender}: hai dimenticato lo slash finale :)"
				$msg .= "/";
			}
		}
		if(preg_match("#^s(\*(.+?))?/(.+)/(.*)/(.*)$#", $msg, $pieces)) {// && preg_match("/{$pieces[3]}/", $wrong[0]["last_said"])) {
			$phrase = $translations->bot_gettext("corrections-theuser"); //"meant";
			$corrected_user = $sender;
			if($pieces[2] != "") {
				$corrected_user = $pieces[2];
				$iduser = $db->check_user($pieces[2]);
				$wrong = $db->select(array("corrections"), array("last_said"), array(""), array("user_IDUser", "chan_IDChan"), array("=",  "="), array($iduser, $idchan));
				$phrase = $translations->bot_gettext("corrections-otheruser"); //"should have meant";
			}
			$oldtext = $pieces[3];
			$newtext = $pieces[4];
			$modifiers = "";
			
			if(preg_match("/i/", $pieces[5]))
				$modifiers = "i";
			if(!preg_match("/r/", $pieces[5]))
				$oldtext = preg_replace("/([\.\/\#\(\)\+\?\*])/", "\\\\$1", $oldtext);

			$action = false;
			if(preg_match("/^\001ACTION (.+)\001$/", $wrong[0]["last_said"]))
				$action = true;

			$wrong[0]["last_said"] = preg_replace("/\001/", "", $wrong[0]["last_said"]);
			$wrong[0]["last_said"] = preg_replace("/ACTION/", "", $wrong[0]["last_said"]);
			$wrong[0]["last_said"] = preg_replace("/&#34;/", "\"", $wrong[0]["last_said"]);
			$wrong[0]["last_said"] = preg_replace("/&#39;/", "'", $wrong[0]["last_said"]);

			$corrected = "";
			if($action)
				$corrected = "* {$corrected_user}:";

			$corrected .= preg_replace("/{$oldtext}/{$modifiers}", $newtext, $wrong[0]["last_said"]);
			if(strlen($corrected) > 0) {
				sendmsg($socket, "{$corrected_user} {$phrase}: " . IRCColours::BOLD . $corrected . IRCColours::Z, $channel);
			}
		} else {
			$msg = preg_replace("/\"/", "&#34;", $msg);
			$msg = preg_replace("/'/", "&#39;", $msg);
			if(count($wrong) > 0) {
				$db->update("corrections", array("last_said"), array($msg), array("user_IDUser", "chan_IDChan"), array("=",  "="), array($iduser, $idchan));
			} else {
				$db->insert("corrections", array("last_said", "user_IDUser", "chan_IDChan"), array($msg, $iduser, $idchan));
			}
		}
	}
?>
