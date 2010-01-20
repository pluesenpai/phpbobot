<?php
	function builtins($irc, $irc_chan, $sender, $cmd)
	{
		global $user_name, $users, $functions, $operators, $db, $auth, $version, $allowed_autolists;
		//Saluti
		if($cmd == "salutami") {
			sendmsg($irc, "Ciao $sender :)", $irc_chan);
		}
		if($cmd == "saluta" || $cmd == "ciao") {
			sendmsg($irc, "Ciao a tutti!! :)", $irc_chan);
		}
		if(preg_match("/^saluta (.*)$/", $cmd, $name)) {
			if(preg_match("/\b([\+%&$~\@])*$name[1]\b/", implode(" ", $users[$irc_chan])) && $name[1] != $user_name)
				sendmsg($irc, "Ciao $name[1]... Come stai??", $irc_chan);
			elseif(count($name) > 1 && $name[1] == $user_name)
				sendmsg($irc, "Chi mi fai salutare? $name[1] sono io!!!", $irc_chan);
			else
				sendmsg($irc, "Chi mi fai salutare? $name[1] non c'&egrave;!!!", $irc_chan);
		}

		//Help & Info
		if($cmd == "shorthelp")
			help($irc, $sender, $functions, true);
		if($cmd == "help")
			help($irc, $sender, $functions);
		if(preg_match("/^help (.+)$/", $cmd, $info))
			help($irc, $sender, $functions, false, $info[1]);
		if($cmd == "version")
			sendmsg($irc, "Sono $user_name, versione $version", $irc_chan);

		//Users
		if(preg_match("/^debanna (.*)$/", $cmd, $name) && in_array($sender, $operators)) {
			sendmsg($irc, "OK... Debanno $name[1]...", $irc_chan);
			send($irc, "MODE $irc_chan -b $name[1]!*@*\n");
		}
		if(preg_match("/^register (.+)$/", $cmd, $password)) {
			aggiungi_user($db, $sender, $irc_chan, $password[1]);
			//sendmsg($irc, "OK! Fatto!", $irc_chan);
			send($irc, "NOTICE $sender :Ora sei registrato!\n");
		}
		if(preg_match("/^auth (.+)$/", $cmd, $password)) {
			$auth[$sender] = $db->verifica_password($sender, $password[1]);
			if($auth[$sender])
				send($irc, "NOTICE $sender :Ora sei autenticato!\n");
			print_r($auth);
		}
		if(preg_match("/^deauth$/", $cmd)) {
			if($auth[$sender]) {
				$auth[$sender] = false;
				send($irc, "NOTICE $sender :Sei stato deautenticato!\n");
			}
		}
		if(preg_match("/^([a-z])op add (.+?)$/", $cmd, $user) && (is_bot_op($sender) || is_channel_owner($sender, $irc_chan) || is_channel_protected_operator($sender,$irc_chan))) {
			$iduser = $db->verifica_user($user[2]);
			$idchan = $db->verifica_chan($irc_chan);
			$result = $db->select(array("entra"), array("modes"), array(""), array("IDChan", "IDUser"), array("=", "="), array($idchan, $iduser));
			if($user[1] == "a")
				$stringa = "+o";
			elseif(in_array($user[1], $allowed_autolists))
				$stringa = "+$user[1]";
			if(strlen($stringa) >= 2) {
				if(count($result) == 0)
					$db->insert("entra", array("IDUser", "IDChan", "IDSaluto", "modes"), array($iduser, $idchan, 0, "'$stringa'"));
				else {
					if(strlen($result[0]['modes']) > 0)
						$stringa = substr($stringa, 1);
					$db->update("entra", array("modes"), array("'$stringa'"), array("IDChan", "IDUser"), array("=", "="), array($idchan, $iduser));
				}
			}
		}

		if(preg_match("/^([a-z])op remove (.+?)$/", $cmd, $user) && (is_bot_op($sender) || is_channel_owner($sender, $irc_chan) || is_channel_protected_operator($sender, $irc_chan))) {
			$iduser = $db->verifica_user($user[2]);
			$idchan = $db->verifica_chan($irc_chan);
			$result = $db->select(array("entra"), array("modes"), array(""), array("IDChan", "IDUser"), array("=", "="), array($idchan, $iduser));
			if($user[1] == "a")
				$stringa = "o";
			elseif(in_array($user[1], $allowed_autolists))
				$stringa = $user[1];
			if(strlen($stringa) >= 1) {
				if(count($result) == 0)
					sendmsg($irc, "$user[2] non fa parte della {$user[1]}op list.", $irc_chan);
				else {
					if(strlen($result[0]['modes']) > 0 && preg_match("/$stringa/", $result[0]['modes'])) {
						$newmode = preg_replace("/$stringa/", "", $result[0]['modes']);
						if(strlen($newmode) == 1)
							$newmode = "";
						$db->update("entra", array("modes"), array("'$newmode'"), array("IDChan", "IDUser"), array("=", "="), array($idchan, $iduser));
					} else
						sendmsg($irc, "$user[2] non fa parte della {$user[1]}op list.", $irc_chan);
				}
			}
		}

		//Messages
		if(preg_match("/^setmessage (.+)$/", $cmd, $message)) {
			if($auth[$sender]) {
				$mess = htmlentities($message[1], ENT_QUOTES, 'UTF-8');
				aggiungi_user($db, $sender, $irc_chan, "", $mess);
				sendmsg($irc, "OK! Fatto!", $irc_chan);
			} else
				sendmsg($irc, "Prima effettua l'autenticazione!", $irc_chan);
		}
		if(preg_match("/^delmessage$/", $cmd, $data)) {
			if($auth[$sender]) {
				elimina_saluto($db, $sender, $irc_chan);
				sendmsg($irc, "Saluto eliminato!", $irc_chan);
			} else
				sendmsg($irc, "Prima effettua l'autenticazione!", $irc_chan);
		}
	}
?>
