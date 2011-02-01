<?php
	function modes($socket, $channel, $sender, $msg, $infos)
	{
		global $registered, $auth, $db;

		if(!$registered[$sender] && !$auth[$sender]) {
			sendmsg($socket, "Tu vuoi cosa? Per fare che?", $channel);
			return;
		}

		$iduser = $db->check_user($sender);
		$idchan = $db->check_chan($channel);
		$result = $db->select(array("enter"), array("modes"), array(""), array("chan_IDChan", "user_IDUser"), array("=", "="), array($idchan, $iduser));

		if(preg_match("/^de/", $infos[0]))
			$mode = "-";
		else
			$mode = "+";
		if(preg_match("/halfop (.+)$/", $infos[0]) && (preg_match("/h|o/", $result[0]["modes"]) || getUserPrivileges($sender, $channel) <= UserLevels::HALFOP_LEVEL || is_bot_op($sender)))
			$mode .= str_repeat("h", substr_count($infos[1], " ") + 1);
		elseif(preg_match("/voice (.+)$/", $infos[0]) && preg_match("/v|h|o/", $result[0]["modes"] || is_bot_op($sender)))
			$mode .= str_repeat("v", substr_count($infos[1], " ") + 1);
		elseif(preg_match("/o/", $result[0]["modes"]) || getUserPrivileges($sender, $channel) <= UserLevels::OPER_LEVEL || is_bot_op($sender))
			$mode .= str_repeat("o", substr_count($infos[1], " ") + 1);

		if(strlen($mode) > 1) {
			send($socket, "MODE $channel $mode {$infos[1]}\n");
		} else {
			sendmsg($socket, "Tu vuoi cosa? Per fare che?", $channel);
		}
	}
?>
