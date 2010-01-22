<?php
	/**
	* @param $user User (Receiver) to search
	* @param $index Index of message to search
	* @return Returns an array of messages (one message) retrieved from the database
	*/
	function __removemsg($user, $index)
	{
		global $db;
		$iduser = $db->check_user($user);
		$db->remove("message", array("IDTo", "IDMsg"), array("=", "="), array($iduser, $index));
	}

	function removemsg($socket, $channel, $sender, $msg, $infos)
	{
		global $translations;

		$number = $infos[1];
		__removemsg($sender, $number);
		sendmsg($socket, sprintf($translations->bot_gettext("messages-removed-%s")), $sender); //"$sender, il messaggio &egrave; stato cancellato correttamente"
	}
?>