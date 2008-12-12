<?php
	include("functions/messages/funcs.php");

	function verifica_user($user)
	{
		global $dbname;

		$dbhandle = new SQLiteDatabase($dbname);
		$query = $dbhandle->query("SELECT IDUser FROM user WHERE username = '$user'");
		$result = $query->fetchAll(SQLITE_ASSOC);

		if(count($result) == 0) {
			$dbhandle->query("INSERT INTO user (username) VALUES ('$user')");

			return $dbhandle->lastInsertRowid();
		}

		return (int)$result[0]['IDUser'];
	}

	function insert_msg($msg, $id_to, $id_sender)
	{
		global $dbname;

		$dbhandle = new SQLiteDatabase($dbname);
		$dbhandle->query("INSERT INTO msg (message, data, letto, notified, IDFrom, IDTo) VALUES ('$msg', '" . date("Y-m-d") . "', 'false', 'false', '$id_sender', '$id_to')");
	}

	function msgsend($socket, $channel, $sender, $msg, $infos)
	{
		create_db();
		$to = $infos[1];
		$post = $infos[2];
		$id_to = verifica_user($to);
		$id_sender = verifica_user($sender);

		insert_msg($post, $id_to, $id_sender);
		sendmsg($socket, "Messaggio inviato, $sender!", $channel);
	}
?>