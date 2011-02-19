<?php
	function modes($socket, $channel, $sender, $msg, $infos)
	{
		global $registered, $auth, $db, $users, $user_name;

		if(!$registered[$sender] && !$auth[$sender]) {
			sendmsg($socket, "Tu vuoi cosa? Per fare che?", $channel);
			return;
		}

		$_users = explode(" ", $infos[1]);
		$userscount = count($_users);

		for($i = 0; $i < $userscount; $i++) {
			$user = $_users[$i];
			if(!preg_match("/\b([\+%&$~\@])*{$user}\b/", implode(" ", $users[$channel]))) {
				sendmsg($socket, "$user non c'è!!", $channel);
				unset($_users[$i]);
			}
		}

		$_users = array_values($_users);
		$userscount = count($_users);
		if($userscount <= 0)
			return;

		$iduser = $db->check_user($sender);
		$idchan = $db->check_chan($channel);
		$result = $db->select(array("enter"), array("modes"), array(""), array("chan_IDChan", "user_IDUser"), array("=", "="), array($idchan, $iduser));

		if(preg_match("/^de/", $infos[0]))
			$mode = "-";
		else
			$mode = "+";
		if(preg_match("/halfop (.+)$/", $infos[0]) && (preg_match("/h|o/", $result[0]["modes"]) || getUserPrivileges($sender, $channel) <= UserLevels::HALFOP_LEVEL || is_bot_op($sender)))
			$mode .= str_repeat("h", $userscount);
		elseif(preg_match("/voice (.+)$/", $infos[0]) && preg_match("/v|h|o/", $result[0]["modes"] || is_bot_op($sender)))
			$mode .= str_repeat("v", $userscount);
		elseif(preg_match("/o/", $result[0]["modes"]) || getUserPrivileges($sender, $channel) <= UserLevels::OPER_LEVEL || is_bot_op($sender))
			$mode .= str_repeat("o", $userscount);

		if(strlen($mode) > 1) {
			send($socket, "MODE $channel $mode " . implode(" ", $_users) . "\n");
		} else {
			sendmsg($socket, "Tu vuoi cosa? Per fare che?", $channel);
		}
	}
?>
