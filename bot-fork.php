#!/usr/bin/php -c/home/paolo/download/bot/php.ini

<?php
	system("clear");
	declare(ticks = 1);

	require_once("Logger.class.php");
	require_once("Config.class.php");

	require_once("database/pdo_sqlite3.php");  //For sqlite3 DB with PDO
	///TODO: Create class for mysql
	///TODO: Create class for mysqli
	///TODO: Create class for sqlite2
	///TODO: Create class for sqlite3
	///TODO: Create class for postgres
	//require_once("database/mysql.php");  //For mysql DB
	//require_once("database/mysqli.php");  //For mysqli DB

	$config = Config::singleton();
	$user_name = $config->getBotName();
	$user_descr = $config->getBotDescription();
	$user_psw = $config->getPassword();
	$irc_chans = $config->getChans();
	$logger = new Logger($user_name, $irc_chans);

	$version = "0.9.10 (beta)";
	$allowed_autolists = array("s", "v", "h");
	$chiusura = false;		//When setted to true the Bot will close
	$functions = array();	//array containing information about functions
	$on_join = array();		//array containing information about on_join functions
	$always = array();		//array containing information about functions that must be called on receiving data from socket
	for($i = 0; $i < count($irc_chans); $i++) {
		$users[$irc_chans[$i]] = array();
		$token[$irc_chans[$i]] = false;
	}

	$irc_server = $config->getServer();
	$irc_port = $config->getPort();
	$colors = false;
	$debug = false;
	$sck_debug = false;
	$slot_saluto = array();
	$party_addr = $config->getListenAddress();
	$party_port = $config->getListenPort();

	if($argc > 1) {
		$options = getopt("s:p:cdga:r:");
		if(array_key_exists('s', $options))
			$irc_server = $options['s'];
		if(array_key_exists('p', $options))
			$irc_port = (int)$options['p'];
		if(array_key_exists('c', $options))
			$colors = true;
		if(array_key_exists('d', $options))
			$debug = true;
		if(array_key_exists('g', $options))
			$sck_debug = true;
		if(array_key_exists('a', $options))
			$party_addr = $options['a'];
		if(array_key_exists('r', $options))
			$party_port = (int)$options['r'];
	}

	require_once("colors.php");
	require_once("database/db.php");
	require_once("common.php");
	require_once("builtins.php");

	echo "\n\n";
	echo "{$BOLD}roBOT for IRC Network{$Z}\n\n\n";
	echo "{$UNDERLINE}Summary of connection data:{$Z}\n\n";
	echo "Server:\t\t$irc_server\n";
	echo "Port:\t\t$irc_port\n";
	echo "Channel(s):\t" . implode(", ", $irc_chans) . "\n";
	echo "UserName:\t$user_name\n";
	echo "Description:\t$user_descr\n";
	echo "Password:\t$user_psw\n";
	echo "\n\n";

	list($functions, $on_join, $always) = getFunctions();

	echo "Creating socket... ";
	$irc = socket_create(AF_INET, SOCK_STREAM, SOL_TCP);
	$connect = socket_connect($irc, $irc_server, $irc_port);
	if($irc && $connect) {
		echo "done!\n";
	} else {
		echo "ERROR!!!!\n";
		die(socket_strerror(socket_last_error()) . " (" . socket_last_error() . ")");
	}

	echo "Creating party-line socket... ";
	$party_mainsck = socket_create(AF_INET, SOCK_STREAM, SOL_TCP);
	if($party_mainsck) {
		echo "done!\n";
		sckdbg($sck_debug, "Socket listening on $party_addr:$party_port");
		if (!socket_set_option($party_mainsck, SOL_SOCKET, SO_REUSEADDR, 1)) {
			die(socket_strerror(socket_last_error()) . " (" . socket_last_error() . ")");
		}
		socket_bind($party_mainsck, $party_addr, $party_port);
		socket_listen($party_mainsck);
	} else {
		echo "ERROR!!!!\n";
		die(socket_strerror(socket_last_error()) . " (" . socket_last_error() . ")");
	}

	echo "\n\n";
	$pid = pcntl_fork();

	if($pid == -1) {
		die("Could not fork");
	} elseif($pid) { //Father
		dbg($debug, "Sending user-name and nick");
		send($irc, "USER $user_name \"1\" \"1\" :$user_descr.\n");
		send($irc, "NICK $user_name\n");
		$party_pid = pcntl_fork();
		if($party_pid == -1) {
			die("Could not fork");
		} elseif($party_pid) { //Father
			pcntl_waitpid($pid, $status);
			socket_shutdown($irc, 2);
			sleep(1);
			socket_close($irc);
			//--------------------------------
			socket_shutdown($party_mainsck, 2);
			socket_close($party_mainsck);
			posix_kill($party_pid, 9);
		} else { //Son
			pcntl_signal(SIGCHLD, "sig_handler");
			do {
				$party_sck = socket_accept($party_mainsck);
				socket_getpeername($party_sck, $party_remoteaddress);
				sckdbg($sck_debug, "New party-line socket connection from $party_remoteaddress!!");
				$another_socket = party_working($party_sck, $db, $irc, $irc_chans);
				socket_close($party_sck);
			} while($another_socket);
		}
	} else { //Son
		pcntl_signal(SIGTERM, "sig_handler");
		pcntl_signal(SIGHUP,  "sig_handler");
		pcntl_signal(SIGUSR1, "sig_handler");
		pcntl_signal(SIGCHLD, "sig_handler");
		foreach($functions as $func)
			include_once("functions/{$func['folder']}/init.php");
		while(!$chiusura) {
			$rawdata = str_replace(array("\n","\r"), "", socket_read($irc, 2048, PHP_NORMAL_READ));
			$data = trim(str_replace("  ", " ", $rawdata));
			if(strlen($data) == 0)
				continue;
			$logger->logMessage($data);
			if($data[0] === ":") {
				@list($d, $type, $recv, $msg) = explode(" ", $data, min(4, substr_count($data, " ") + 1));
				dbg($debug, "\$d: $d");
				dbg($debug, "\$type: $type");
				dbg($debug, "\$recv: $recv");
				dbg($debug, "\$msg: $msg");
				$d = substr($d, 1);
				if(strpos($d, "!") !== false) {
					preg_match("/(.*)!.*/", $d, $sender);
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

				if($type == "353") {  //Ricevo l'output di names
					$read_users = explode(" ", $msg);
					$chan = $read_users[1];
					for($c = 0; $c < 2; $c++)
						unset($read_users[$c]);
					$read_users[$c] = substr($read_users[$c], 1); //Tolgo i : dall'inizio del nome.
					$users[$chan] = array_values($read_users);
					$utenti = combina_array($users);
					foreach($users[$chan] as $user) {
						$utente = clean_username($user);
						if(!isset($auth[$utente]))
							$auth[$utente] = false;
					}
					$auth_a = array_keys($auth);
					foreach($auth_a as $utente) {
						if(!in_array($utente, $utenti))
							unset($auth[$utente]);
					}
					if($token[$chan]) {
						$token[$chan] = false;
						foreach($on_join as $join_func) {
							foreach($users[$chan] as $u) {
								chiama($join_func['folder'], $join_func['name'], $irc, $chan, clean_username($u), $msg, array("bot_join"));
							}
						}
					}
// 					dbg($debug, implode(" ", $users));
				}

				if(isset($slot_saluto[0])) {
					for($i = 0; $i < count($slot_saluto); $i++) {
						if(microtime(true) - $slot_saluto[$i][3] >= 0.05) {
							$slot_saluto[$i][0]--;
							if($slot_saluto[$i][0] == 3)
								send($irc, "NAMES {$slot_saluto[$i][2]}\n");
						}
					}
					if($slot_saluto[0][0] <= 0) {
						$slot_saluto[0][0] = is_user_in_chan($slot_saluto[0][1], $slot_saluto[0][2]);
						if($slot_saluto[0][0] == true) {
							$s = $slot_saluto[0][1];
							$i = $slot_saluto[0][2];
							$joiner_mess = saluto($db, $s, $i);
							$joiner_mode = mode($db, $s, $i);
// 							if(strcmp($s, $user_name) != 0) {
								sendmsg($irc, "Ciao $s", $i, 0, true);
								if(strlen($joiner_mess) > 0)
									sendmsg($irc, "[$s]: $joiner_mess", $i, 0, true);
								sendmsg($irc, "Per informazioni dai il comando \"$user_name help\"!!!", $i, 0, true);
								$mode_len = strlen($joiner_mode);
								if($mode_len > 0) {
									$stringa_mode = "MODE $i $joiner_mode ";
									for($index = 1; $index < $mode_len; $index++)
										$stringa_mode .= $s . " ";
									send($irc, $stringa_mode . "\n");
									dbg($debug, $stringa_mode);
								}
// 							}
							foreach($on_join as $join_func) {
								chiama($join_func['folder'], $join_func['name'], $irc, $i, $s, $msg, array("on_join"));
							}
						}
						unset($slot_saluto[0]);
						$slot_saluto = array_values($slot_saluto);
					}
				}
				if(in_array(strtolower($type), array("nick", "quit", "mode", "join", "part"))) {
					if($type == "mode" || $sender != $user_name)
						send($irc, "NAMES $irc_chan\n");
				}
				if(strtolower($type) == "join" && !is_cop($d)) {
					dbg($debug, "New join");
					dbg($debug, "\$joiner = $sender");
					$irc_chan = substr($recv, 1);
					if(strcmp($sender, $user_name) != 0)
						$slot_saluto[] = array(4, $sender, $irc_chan, microtime(true));
				} elseif(($type == "376") || ($type == "422")) {
					dbg($debug, "Codice 376 o codice 422 ricevuto... Procedo con join e login");
					if(isset($user_psw) && strlen($user_psw) != 0)
						sendmsg($irc, "IDENTIFY $user_psw", "NickServ");
					else {
						foreach($irc_chans as $irc_chan) {
							entra_chan($irc_chan);
						}
					}
				} elseif($type == "433") {
					dbg($debug, "Codice 433 ricevuto, necessario GHOST");
					sendmsg($irc, "GHOST $user_name $user_psw", "NickServ");
					sendmsg($irc, "IDENTIFY $user_psw", "NickServ");
				} elseif(($type == "NOTICE" && $sender == "NickServ" && $msg == "Password accettata - adesso sei riconosciuto.") || ($type == "401" && $msg == "ickServ :No such nick/channel")) {
					///TODO: Sistemare queste condizioni!!! Altrimenti funziona solo su un server localizzato in ITA
					foreach($irc_chans as $irc_chan) {
						entra_chan($irc_chan);
					}
				} else {
					//From here all functions
					foreach($always as $always_func) {
						chiama($always_func['folder'], $always_func['name'], $irc, $irc_chan, $sender, $msg, array("always", $type, $data));
					}
					if($recv == $user_name) {
						$regex = "/^(.*)$/";
						$num = 1;
					} else {
						$regex = "/^({$user_name}[- ,;.:!?]*[ ]+|!)(.*)$/";
						$num = 2;
					}
					if(preg_match($regex, $msg, $ret)) {
						for($i = 0; $i < $num; $i++)
							unset($ret[$i]);
						$ret = array_values($ret);
						$cmd = implode(" ", $ret);
						dbg($debug, "\$cmd = $cmd");
						if($cmd == "sparati" && in_array($sender, $operators)) {
							sendmsg($irc, "Ok... :(", $irc_chan, 0, true);
							foreach($irc_chans as $c) {
								sendmsg($irc, "Addio!!! :'(", $c, 1 / count($irc_chans), true);
								sendmsg($irc, "BANG!", $c, 1 / count($irc_chans), true);
							}
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
						if(!$trovato)
							builtins($irc, $irc_chan, $sender, $cmd);
					} elseif(preg_match("/^{$user_name}[ \,;\.:\-!\?]*$/", $msg)) {
						sendmsg($irc, "Cosa c'&egrave; $sender??", $irc_chan);
					}
				}
			} else {
				list($type, $msg) = explode(" ", trim($data));
				echo "{$col}<<---   $data{$col_}\n";
				if(strtolower($type) == "ping") {
					dbg($debug, "PING request");
					send($irc, "PONG " . substr($msg, 1) . "\n");
				}
			}
		}
		sendwait($irc, "QUIT bye bye!", 0, true);
		posix_kill(posix_getpid(), SIGTERM);
	}
?>
