<?php
	function corrections($socket, $channel, $sender, $msg, $infos)
	{
		global $db;
	
		if($infos[1] != "PRIVMSG")
			return;

		$iduser = $db->check_user($sender);
		$idchan = $db->check_chan($channel);
		$wrong = $db->select(array("corrections"), array("last_said"), array(""), array("user_IDUser", "chan_IDChan"), array("=",  "="), array($iduser, $idchan));

		if(preg_match("#^s(\*(.+?))?/(.+)/(.*)[^/]$#", $msg)) {
			sendmsg($socket, "Fai pi&ugrave; attenzione {$sender}: hai dimenticato lo slash finale :)", $channel);
			$msg .= "/";
		}
		if(preg_match("#^s(\*(.+?))?/(.+)/(.*)/(.*)$#", $msg, $pieces)) {// && preg_match("/{$pieces[3]}/", $wrong[0]['last_said'])) {
			$phrase = "meant";
			$corrected_user = $sender;
			if($pieces[2] != "") {
				$corrected_user = $pieces[2];
				$iduser = $db->check_user($pieces[2]);
				$wrong = $db->select(array("corrections"), array("last_said"), array(""), array("user_IDUser", "chan_IDChan"), array("=",  "="), array($iduser, $idchan));
				$phrase = "should have meant";
			}
			$oldtext = $pieces[3];
			$newtext = $pieces[4];
			$modifiers = "";
			if(preg_match("/i/", $pieces[5]))
				$modifiers = "i";
			if(preg_match("/r/", $pieces[5]))
				$oldtext = preg_replace("/([\.\/\#\(\)\+\?\*])/", "\\\\$1", $oldtext);

			$corrected = preg_replace("/{$oldtext}/{$modifiers}", $newtext, $wrong[0]['last_said']);
			if(strlen($corrected) > 0) {
				sendmsg($socket, "{$corrected_user} {$phrase}: \002$corrected\002", $channel);
			}
		} else {
			if(count($wrong) > 0) {
				$db->update("corrections", array("last_said"), array($msg), array("user_IDUser", "chan_IDChan"), array("=",  "="), array($iduser, $idchan));
			} else {
				$db->insert("corrections", array("last_said", "user_IDUser", "chan_IDChan"), array($msg, $iduser, $idchan));
			}
		}
	}
?>
