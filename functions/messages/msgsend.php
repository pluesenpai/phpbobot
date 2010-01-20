<?php
	function insert_msg($msg, $id_to, $id_sender)
	{
		global $db;

		$db->insert("message", array("message", "data", "letto", "notified", "IDFrom", "IDTo"), array($msg, date("Y-m-d"), "false", "false", $id_sender, $id_to));
	}

	function msgsend($socket, $channel, $sender, $msg, $infos)
	{
		global $db;
		$to = $infos[1];
		$post = $infos[2];
		$id_to = $db->check_user($to);
		$id_sender = $db->check_user($sender);

		insert_msg($post, $id_to, $id_sender);
		sendmsg($socket, "Messaggio inviato, $sender!", $channel);
	}
?>