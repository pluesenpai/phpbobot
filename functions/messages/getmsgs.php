<?php
	/**
	* @param $user User (Receiver) to search
	* @param $to User (Sender of message) to search
	* @param $all Type of search. false means only unread messages, true all messages
	* @return Returns an array of messages retrieved from the database
	*/
	function __getmsgs($user, $to = "", $all = false, $silent = false)
	{
		global $db;

		$cond_f = array("IDFrom", "IDTo", "User_To");
		$cond_o = array("=", "=", "=");
		$cond_v = array("IDUser", "IDUser", $user);

		if(!$all) {
			$cond_f[] = "letto";
			$cond_o[] = "=";
			$cond_v[] = "false";
		}
		if($to != "") {
			$cond_f[] = "User_From";
			$cond_o[] = "=";
			$cond_v[] = $to;
		}
		if($silent) {
			$cond_f[] = "notified";
			$cond_o[] = "=";
			$cond_v[] = "false";
		}

		$result = $db->select(array("message", "user"), array("IDMsg", "data", "username", "username", "letto"), array("", "", "User_To", "User_From", ""), $cond_f, $cond_o, $cond_v);

		foreach($result as $msg)
			$db->update("message", array("notified"), array("true"), array("IDMsg"), array("="), array($msg['IDMsg']));

		return $result;
	}

	/**
	* @param $user User (Receiver) to search
	* @param $index Index of message to search
	* @param $to User (Sender of message) to search
	* @param $all Type of search. false means only unread messages, true all messages
	* @return Returns an array of messages (one message) retrieved from the database
	*/
	function __getmsg($user, $index, $to = "", $all = false)
	{
		global $db;

		$cond_f = array("IDFrom", "IDTo", "User_To", "IDMsg");
		$cond_o = array("=", "=", "=", "=");
		$cond_v = array("IDUser", "IDUser", $user, $index);

		if(!$all) {
			$cond_f[] = "letto";
			$cond_o[] = "=";
			$cond_v[] = "false";
		}
		if($to != "") {
			$cond_f[] = "User_From";
			$cond_o[] = "=";
			$cond_v[] = $to;
		}

		$result = $db->select(array("message", "user"), array("IDMsg", "message", "data", "username", "username", "letto"), array("", "", "", "User_To", "User_From", ""), $cond_f, $cond_o, $cond_v);

		foreach($result as $msg)
			$db->update("message", array("letto"), array("true"), array("IDMsg"), array("="), array($msg['IDMsg']));

		return $result;
	}

	function getmsgs($socket, $channel, $sender, $msg, $infos)
	{
		global $translations;

		$number = -1;
		$to = "";
		$all = $silent = false;

		if(count($infos) >= 1) {
			//if(preg_match("/^(allmessages|readall)/", $infos[0]))
			if(preg_match("/\+$/", $infos[0]))
				$all = true;
			if(preg_match("/on_join/", $infos[0]))
				$silent = true;
			if(preg_match("/bot_join/", $infos[0]))
				$silent = true;
		}

		if(count($infos) > 1) {
			if(is_numeric($infos[1]))
				$number = $infos[1];
			else
				$to = $infos[1];
		}

		if($number != -1)
			$msgs = __getmsg($sender, $number, $to, $all);
		else
			$msgs = __getmsgs($sender, $to, $all, $silent);

		$cnt = count($msgs);

		if($cnt > 0 || ($cnt == 0 && !$silent)) {
			if($all)
				$letti = "";
			else {
				if($cnt == 1) {
					$letti = $translations->bot_gettext("messages-getmsgs-read_singular");
					$msg = $translations->bot_gettext("messages-getmsgs-msg_singular");
				} else {
					$letti = $translations->bot_gettext("messages-getmsgs-read_plural");
					$msg = $translations->bot_gettext("messages-getmsgs-msg_plural");
				}
				//$letti = " non lett" . ($cnt == 1 ? "o" : "i");
			}
			sendmsg($socket, sprintf($translations->bot_gettext("messages-getmsgs-infos-%s-%s-%s-%s"), $sender, $cnt, $msg, $letti), $sender); //"$sender, hai $cnt $msg $letti"
			foreach($msgs as $msg) {
				sendmsg($socket, "{$msg['IDMsg']}) Sender: {$msg['User_From']}, Date: " . date("d F Y", strtotime($msg['data'])), $sender);
				if($number != -1)
					sendmsg($socket, "\t\t{$msg['message']}", $sender);
			}
		}
	}
?>
