<?php
	include("functions/messages/funcs.php");

	/**
	* @param $user User (Receiver) to search
	* @param $to User (Sender of message) to search
	* @param $all Type of search. false means only unread messages, true all messages
	* @return Returns an array of messages retrieved from the database
	*/
	function __getmsgs($user, $to = "", $all = false, $silent = false)
	{
		global $dbname;

		$where = $all ? "" : " AND msg.letto = 'false'";
		$where1 = ($to == "") ? "" : " WHERE User_From = '$to'";
		$where2 = !$silent ? "" : " AND notified = 'false'";

		$dbhandle = new SQLiteDatabase($dbname);
		$query = $dbhandle->query("SELECT IDMsg, data, User_To, username AS User_From, letto FROM (SELECT IDMsg, data, username AS User_To, IDFrom, letto, notified FROM msg LEFT JOIN user ON msg.IDTo = user.IDUser WHERE User_To = '$user'{$where}{$where2}) LEFT JOIN user ON IDFrom = user.IDUser$where1");
		$result = $query->fetchAll(SQLITE_ASSOC);
		foreach($result as $msg) {
			$query = $dbhandle->query("UPDATE msg SET notified = 'true' WHERE IDMsg = " . $msg['IDMsg']);
		}
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
		global $dbname;

		$where = $all ? "" : " AND msg.letto = 'false'";
		$where1 = ($to == "") ? "" : " WHERE User_From = '$to'";

		$dbhandle = new SQLiteDatabase($dbname);
		$query = $dbhandle->query("SELECT IDMsg, message, data, User_To, username AS User_From, letto FROM (SELECT IDMsg, message, data, username AS User_To, IDFrom, letto FROM msg LEFT JOIN user ON msg.IDTo = user.IDUser WHERE User_To = '$user'$where AND IDMsg = $index) LEFT JOIN user ON IDFrom = user.IDUser$where1");
		$result = $query->fetchAll(SQLITE_ASSOC);
		foreach($result as $msg) {
			$query = $dbhandle->query("UPDATE msg SET letto = 'true' WHERE IDMsg = " . $msg['IDMsg']);
		}
		return $result;
	}

	function getmsgs($socket, $channel, $sender, $msg, $infos)
	{
		create_db();

		$number = -1;
		$to = "";
		$all = $silent = false;

		if(count($infos) >= 1) {
			if(ereg("^(allmessages|readall)", $infos[0]))
				$all = true;
			if(ereg("on_join", $infos[0]))
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
			else
				$letti = " non lett" . ($cnt == 1 ? "o" : "i");
			sendmsg($socket, "$sender, hai $cnt " . ($cnt == 1 ? "messaggio" : "messaggi") . $letti , $sender);
			foreach($msgs as $msg) {
				sendmsg($socket, "{$msg['IDMsg']}) Sender: {$msg['User_From']}, Date: " . date("d F Y", strtotime($msg['data'])), $sender);
				if($number != -1)
					sendmsg($socket, "\t\t{$msg['message']}", $sender);
			}
		}
	}
?>
