<?php
	function builtins($irc, $irc_chan, $sender, $cmd)
	{
		global $user_name, $users, $functions, $operators, $db, $auth, $registered, $parla, $irc_chans;

		$allowed_autolists = array(
			"a" => "o",
			"s" => "a",
			"v" => "v",
			"h" => "h",
			"q" => "q"
		);

		//Saluti
		if($cmd == "salutami") {
			sendmsg($irc, "Ciao {$sender} :)", $irc_chan);
		}
		if($cmd == "saluta" || $cmd == "ciao") {
			sendmsg($irc, "Ciao a tutti!! :)", $irc_chan);
		}
		if(preg_match("/^saluta (.*)$/", $cmd, $name)) {
			if(preg_match("/\b([\+%&$~\@])*{$name[1]}\b/", implode(" ", $users[$irc_chan])) && $name[1] != $user_name)
				sendmsg($irc, "Ciao {$name[1]}... Come stai??", $irc_chan);
			elseif(count($name) > 1 && $name[1] == $user_name)
				sendmsg($irc, "Chi mi fai salutare? {$name[1]} sono io!!!", $irc_chan);
			else
				sendmsg($irc, "Chi mi fai salutare? {$name[1]} non c'&egrave;!!!", $irc_chan);
		}

		//Silenziatore
		if($cmd == "zitto") {
			sendmsg($irc, "...", $irc_chan);
			$parla[$irc_chan] = false;
		}
		if($cmd == "bai a marrai") {
			foreach($irc_chans as $index => $value) {
				sendmsg($irc, "Vado a zappare!", $value);
				$parla[$value] = false;
			}
			send($irc, "AWAY :Vado a zappare!\n");
		}
		if($cmd == "parla") {
			$parla[$irc_chan] = true;
			sendmsg($irc, "Blablabla Blabla", $irc_chan);
		}
		if($cmd == "torra") {
			send($irc, "AWAY\n");
			foreach($irc_chans as $index => $value) {
				$parla[$value] = true;
				sendmsg($irc, "Torrau!!", $value);
			}
		}

		//Help & Info
		if($cmd == "shorthelp")
			help($irc, $sender, $functions, true);
		if($cmd == "help")
			help($irc, $sender, $functions);
		if(preg_match("/^help (.+)$/", $cmd, $info))
			help($irc, $sender, $functions, false, $info[1]);
		if($cmd == "version")
			sendmsg($irc, "Sono $user_name, versione " . version, $irc_chan);

		//Users
						if(preg_match("/^debanna (.*)$/", $cmd, $name) && is_bot_op($sender) && ($registered[$sender] || $auth[$sender])) {
							sendmsg($irc, "OK... Debanno {$name[1]}...", $irc_chan);
							send($irc, "MODE {$irc_chan} -b {$name[1]}!*@*\n");
						}
		if(preg_match("/^register (.+)$/", $cmd, $password)) {
			$db->add_user($sender, $irc_chan, $password[1]);
			send($irc, "NOTICE {$sender} :Ora sei registrato!\n");
		}
		if(preg_match("/^auth (.+)$/", $cmd, $password)) {
			$auth[$sender] = $db->check_password($sender, $password[1]);
			if($auth[$sender])
				send($irc, "NOTICE {$sender} :Ora sei autenticato!\n");
		}
		if(preg_match("/^deauth$/", $cmd)) {
			if($auth[$sender]) {
				$auth[$sender] = false;
				send($irc, "NOTICE {$sender} :Sei stato deautenticato!\n");
			}
		}
		if(preg_match("/^([a-z])op add (.+?)$/", $cmd, $user) && is_bot_op($sender) && ($registered[$sender] || $auth[$sender])) {
			$iduser = $db->check_user($user[2]);
			$idchan = $db->check_chan($irc_chan);
			$result = $db->select(array("enter"), array("modes"), array(""), array("chan_IDChan", "user_IDUser"), array("=", "="), array($idchan, $iduser));
			if(array_key_exists($user[1], $allowed_autolists)) {
				$stringa = $allowed_autolists[$user[1]];
			/*if($user[1] == "a")
				$stringa = "o";
			elseif(in_array($user[1], $allowed_autolists))
				$stringa = "$user[1]";*/
				if(strlen($stringa) >= 1) {
					if(count($result) == 0)
						$db->insert("enter", array("user_IDUser", "chan_IDChan", "greet_IDGreet", "modes"), array($iduser, $idchan, 0, $stringa));
					else {
						if(!preg_match("/$stringa/", $result[0]["modes"])) { //strlen($result[0]["modes"]) > 0 &&
							$stringa = $result[0]["modes"] . $stringa;
							$db->update("enter", array("modes"), array($stringa), array("chan_IDChan", "user_IDUser"), array("=", "="), array($idchan, $iduser));
						}
					}
					sendmsg($irc, "{$user[2]} &egrave; stato aggiunto alla {$user[1]}op list.", $irc_chan);
				}
			} else
				sendmsg($irc, "Lista {$user[1]}op non esistente.", $irc_chan);
		}

		if(preg_match("/^([a-z])op remove (.+?)$/", $cmd, $user) && is_bot_op($sender) && ($registered[$sender] || $auth[$sender])) {
			$iduser = $db->check_user($user[2]);
			$idchan = $db->check_chan($irc_chan);
			$result = $db->select(array("enter"), array("modes"), array(""), array("chan_IDChan", "user_IDUser"), array("=", "="), array($idchan, $iduser));
			$aop_remove_index = array_search($user[1], $allowed_al_input);
			if(array_key_exists($user[1], $allowed_autolists)) {
				$stringa = $allowed_autolists[$user[1]];
// 			if($user[1] == "a")
// 				$stringa = "o";
// 			elseif(in_array($user[1], $allowed_autolists))
// 				$stringa = $user[1];
				if(strlen($stringa) >= 1) {
					if(count($result) == 0)
						sendmsg($irc, "{$user[2]} non fa parte della {$user[1]}op list.", $irc_chan);
					else {
						if(strlen($result[0]["modes"]) > 0 && preg_match("/$stringa/", $result[0]["modes"])) {
							$newmode = preg_replace("/$stringa/", "", $result[0]["modes"]);
							if(strlen($newmode) == 1)
								$newmode = "";
							$db->update("enter", array("modes"), array($newmode), array("chan_IDChan", "user_IDUser"), array("=", "="), array($idchan, $iduser));
							sendmsg($irc, "{$user[2]} &egrave; stato rimosso dalla {$user[1]}op list.", $irc_chan);
						} else
							sendmsg($irc, "{$user[2]} non fa parte della {$user[1]}op list.", $irc_chan);
					}
				}
			} else
				sendmsg($irc, "Lista {$user[1]}op non esistente.", $irc_chan);
		}

		//Messages
		if(preg_match("/^setmessage (.+)$/", $cmd, $message)) {
			if($auth[$sender]) {
				$mess = htmlentities($message[1], ENT_QUOTES, 'UTF-8');
				//aggiungi_user($db, $sender, $irc_chan, "", $mess);
				$db->add_user($sender, $irc_chan, "", $mess);
				sendmsg($irc, "OK! Fatto!", $irc_chan);
			} else
				sendmsg($irc, "Prima effettua l'autenticazione!", $irc_chan);
		}
		if(preg_match("/^delmessage$/", $cmd, $data)) {
			if($auth[$sender]) {
				//elimina_saluto($db, $sender, $irc_chan);
				$db->del_greet($sender, $irc_chan);
				sendmsg($irc, "Saluto eliminato!", $irc_chan);
			} else
				sendmsg($irc, "Prima effettua l'autenticazione!", $irc_chan);
		}
	}
?>
