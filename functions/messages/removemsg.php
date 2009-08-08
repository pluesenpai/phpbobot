<?php
	include("functions/messages/funcs.php");

	/**
	* @param $user User (Receiver) to search
	* @param $index Index of message to search
	* @return Returns an array of messages (one message) retrieved from the database
	*/
	function __removemsg($user, $index)
	{
// 		global $dbname;
//
// 		$dbhandle = new SQLiteDatabase($dbname);
// 		$query = $dbhandle->query("SELECT IDUser FROM user WHERE username = '$user'");
// 		$result = $query->fetchAll(SQLITE_ASSOC);
// 		foreach($result as $r) {
// 			$query = $dbhandle->query("DELETE FROM msg WHERE IDTo = '{$r['IDUser']}' AND IDMsg = $index");
// 		}
		global $db;
		$iduser = $db->verifica_user($user);
		$db->remove("message", array("IDTo", "IDMsg"), array("=", "="), array($iduser, $index));
	}

	function removemsg($socket, $channel, $sender, $msg, $infos)
	{
		$number = $infos[1];
		__removemsg($sender, $number);
		sendmsg($socket, "$sender, il messaggio è stato cancellato correttamente", $sender);
	}
?>