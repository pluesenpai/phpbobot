<?php
	system("clear");
	declare(ticks = 1);

	include_once("common.php");

	$irc_server = "irc.syrolnet.org";
	$irc_port = 6668;
	$irc_chans = array("#sardylan");
	$user_name = "bobot";
	$user_psw = "E_CHE_LA_VENGO_A_DIRE_A_VOI?!?!?!?!?!";

	$debug = false;
	$colors = true;
	$chiusura = false;		//When setted to true the Bot will close
	$functions = array();	//array containing information about functions
	$on_join = array();		//array containing information about on_join functions
	$always = array();		//array containing information about functions that must be called on receiving a PRIVMSG
	for($i = 0; $i < count($irc_chans); $i++) {
		$users[$irc_chans[$i]] = array();
	}

	include_once("colors.php");

	echo "\n\n";
	echo "{$BOLD}roBOT for IRC Network{$Z}\n\n\n";
	echo "{$UNDERLINE}Summary of connection data:{$Z}\n\n";
	echo "Server:\t\t$irc_server\n";
	echo "Port:\t\t$irc_port\n";
	echo "Channel(s):\t" . implode(", ", $irc_chans) . "\n";
	echo "UserName:\t$user_name\n";
	echo "Password:\t$user_psw\n";
	echo "\n\n";

	list($functions, $on_join, $always) = getFunctions();

	echo "Creating socket... ";
	$irc = fsockopen($irc_server, $irc_port, $irc_errno, $irc_errstr, 15);
	if($irc) {
		echo "done!\n";
	} else {
		echo "ERROR!!!!\n";
		die($irc_errstr . " ($irc_errno)");
	}

	echo "\n\n";
	$pid = pcntl_fork();

	if($pid == -1) {
		die("Could not fork");
	} elseif($pid) { //Father
		dbg($debug, "Sending user-name and nick");
		send($irc, "USER $user_name \"1\" \"1\" :Bot for Filetor CHAN.\n");
		send($irc, "NICK $user_name\n");
		pcntl_wait($status);
		fclose($irc);
	} elseif(!$pid) { //Son
		pcntl_signal(SIGTERM, "sig_handler");
		pcntl_signal(SIGHUP,  "sig_handler");
		pcntl_signal(SIGUSR1, "sig_handler");
		foreach($functions as $func)
			include_once("functions/{$func['folder']}/init.php");
		while (!feof($irc) && $chiusura == false) {
			$rawdata = str_replace(array("\n","\r"), "", fgets($irc, 512));
			$data = trim(str_replace("  ", " ", $rawdata));
			if(strlen($data) == 0)
				continue;
			if($data[0] === ":") {
				list($d, $type, $recv, $msg) = explode(" ", $data, 4);
				$d = substr($d, 1);
				if(strpos($d, "!") !== false) {
					ereg("(.*)!.*", $d, $sender);
					$sender = $sender[1];
				} else
					$sender = $d;
				$msg = substr($msg, 1);
				if($recv == $user_name)
					$irc_chan = $sender;
				else
					$irc_chan = $recv;
				if($recv == $user_name) {
					$col = $LGREEN;
					$col_ = $Z;
				} else {
					$col = $col_ = "";
				}
				echo "{$col}<<---   $data{$col_}\n";

				if(in_array(strtolower($type), array("nick", "quit", "mode", "join", "part"))) {
					if($type == "mode" || $sender != $user_name)
						send($irc, "NAMES $irc_chan\n");
				}
				if(strtolower($type) == "join") {
					dbg($debug, "New join");
					dbg($debug, "\$joiner = $sender");
					$irc_chan = substr($recv, 1);
					$xml = simplexml_load_file("welcome.xml");
					foreach($xml->joiner as $joiner_info) {
						$joiner_name = $joiner_info->name;
						$joiner_mess = $joiner_info->mess;
						dbg($debug, "\$joiner_name = $joiner_name");
						dbg($debug, "\$joiner_mess = $joiner_mess");
						if(strcmp($joiner_name, $sender) == 0)
							break;
					}
					if(strcmp($joiner_name, $sender) == 0) {
						sendmsg($irc, "Ciao $joiner_name", $irc_chan);
						sendmsg($irc, "[$joiner_name]: $joiner_mess", $irc_chan);
						sendmsg($irc, "Per informazioni dai il comando \"$user_name help\"!!!", $irc_chan);
						$mode_len = strlen($joiner_info->mode);
						$stringa_mode = "MODE $irc_chan $joiner_info->mode ";
						for($index = 1; $index < $mode_len; $index++)
							$stringa_mode .= $joiner_name . " ";
						send($irc, $stringa_mode . "\n");
						dbg($debug, $stringa_mode);
					}
					foreach($on_join as $join_func) {
						chiama($join_func['folder'], $join_func['name'], $irc, $irc_chan, $sender, $msg, array("on_join"));
					}
				} elseif(($type == "376") || ($type == "422")) {
					dbg($debug, "Codice 376 o codice 422 ricevuto... Procedo con join e login");
					if(isset($user_psw) && strlen($user_psw) != 0)
						sendmsg($irc, "IDENTIFY $user_psw", "NickServ");
					foreach($irc_chans as $irc_chan) {
						send($irc, "JOIN $irc_chan\n");
						sendmsg($irc, "Ciao a tutti... $user_name &egrave; tornato!", $irc_chan, 1, true);
						sendmsg($irc, "Ora controllo il canale!!!", $irc_chan);
						sendmsg($irc, "Per informazioni dai il comando \"$user_name help\"!!!", $irc_chan);
					}
				} elseif($type == "433") {
					dbg($debug, "Codice 433 ricevuto, necessario GHOST");
					sendmsg($irc, "GHOST $user_name $user_psw", "NickServ");
					sendmsg($irc, "IDENTIFY $user_psw", "NickServ");
					foreach($irc_chans as $irc_chan) {
						send($irc, "JOIN $irc_chan\n");
						sendmsg($irc, "Ciao a tutti... $user_name &egrave; tornato!", $irc_chan, 1, true);
						sendmsg($irc, "Ora controllo il canale!!!", $irc_chan);
						sendmsg($irc, "Per informazioni dai il comando \"$user_name help\"!!!", $irc_chan);
					}
				} elseif($type == "353") {  //Ricevo l'output di names
					$read_users = explode(" ", $msg);
					$chan = $read_users[1];
					for($c = 0; $c < 2; $c++)
						unset($read_users[$c]);
					$read_users[$c] = substr($read_users[$c], 1); //Tolgo i : dall'inizio del nome.
					$users[$chan] = array_values($read_users);
					//dbg($debug, implode(" ", $users));
					//print_r($users);
				} else {
					$array = explode(" ", $msg);
					for($j = 3; $j < count($array); $j++) {
						$parola = strtolower($array[$j]);
						dbg($debug, "Parola in posizione $j: $parola");
						if($moderated) {
							if(!in_array($sender, $operators) && array_search($parola, $bad_words) !== false) {
								sendmsg($irc, "$sender::: La parola $parola non &egrave; ammessa!!!", $irc_chan);
								sendmsg($irc, "$sender::: Ti prendo a calci!!!", $irc_chan);
								send($irc, "KICK $irc_chan $sender\n");
								$kickkati[$sender]++;
								dbg($debug, "Kick: $sender: $kickkati[$sender]");
							}
							if(array_search($parola, $bad_words) !== FALSE && !in_array($sender, $operators)) {
								sendmsg($irc, "$sender::: La parola $parola non &egrave; ammessa!!!", $irc_chan);
								sendmsg($irc, "$sender::: E siamo a $kickkati[$sender]...", $irc_chan);
								$kickkati[$sender]++;
								if($kickkati[$sender] == $max_kicks - 1)
									sendmsg($irc, "$sender::: Alla prossima ti butto FUORI!!!", $irc_chan);
								elseif($kickkati[$sender] == $max_kicks) {
									sendmsg($irc, "$sender::: Ti avevo avertito!!!", $irc_chan);
									sendmsg($irc, "$sender::: FFUUOORRIIIIIII!!!", $irc_chan);
									sendmsg($irc, "MODE $irc_chan +b $sender!*@*\n");
									sendmsg($irc, "Peggio per lui... Si arrangia!!! AHAHAHAH ;)");
									$kickkati[$sender] = 0;
								}
							}
						}
					}
					//From here all functions
					foreach($always as $always_func) {
						chiama($always_func['folder'], $always_func['name'], $irc, $irc_chan, $sender, $msg, array("always"));
					}
					if($recv == $user_name)
						$regex = "/^(.*)$/";
					else
						$regex = "/^{$user_name}[ \,;.:\-!\?]*[ ]+(.*)$/";
					///NOTE: Eliminata divisione di $data in $d[], quindi ho cambiato la vecchia regex: /^{$user_name}[ \,;.:\-!\?]*$/
					if(preg_match($regex, $msg, $ret)) { // Accetta tutti i $user_name + una combinazione (lunga quanto vuoi) dei char nelle [ ]...
						unset($ret[0]);
						$ret = array_values($ret);
						$cmd = implode(" ", $ret);
						dbg($debug, "\$cmd = $cmd");
						if($cmd == "salutami") {
							sendmsg($irc, "Ciao $sender :)", $irc_chan);
						}
						if($cmd == "saluta") {
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
						if($cmd == "ciao") {
							sendmsg($irc, "Ciao a tutti!! :)", $irc_chan);
						}
						if($cmd == "help")
							help($irc, $sender, $functions);
						if($cmd == "sparati" && in_array($sender, $operators)) {
							sendmsg($irc, "Ok... :( Addio!!!", $irc_chan, 1, true);
							sendmsg($irc, "BANG!", $irc_chan);
							$chiusura = true;
						}
						$trovato = false;
						for($i = 0; ($i < count($functions)) && ($trovato == false); $i++) {
							$folder = $functions[$i]['folder'];
							$fun = $functions[$i]['name'];
							$priv = $functions[$i]['privileged'];
							$regex = $functions[$i]['regex'];
							if($priv == 1) {
								if(preg_match($regex, $cmd, $infos) && in_array($sender, $operators)) {
									chiama($folder, $fun, $irc, $irc_chan, $sender, $msg, $infos);
									$trovato = true;
								}
							} else {
								if(preg_match($regex, $cmd, $infos)) {
									chiama($folder, $fun, $irc, $irc_chan, $sender, $msg, $infos);
									$trovato = true;
								}
							}
						}
						if(preg_match("/^debanna (.*)$/", $cmd, $name) && in_array($sender, $operators)) {
							if(count($d) > 5) {
								sendmsg($irc, "OK... Debanno $name[1]...", $irc_chan);
								send($irc, "MODE $irc_chan -b $name[1]!*@*\n");
							}
						}
					} elseif(preg_match("/^{$user_name}[ \,;.:\-!\?]*$/", $msg)) {
						sendmsg($irc, "Cosa c'&egrave; $sender??", $irc_chan);
					}
				}
			} else {
				list($type, $msg) = explode(" ", trim($data));
				if(strtolower($type) == "ping") {
					dbg($debug, "PING request");
					send($irc, "PONG " . substr($msg, 1) . "\n");
				}
			}
		}
		posix_kill(posix_getpid(), 9);
	}
?>
