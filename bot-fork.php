#!/usr/bin/php -c./php5.3.ini

<?php
	system("clear");
	declare(ticks = 1);

	require_once("Config.class.php");
	$config = Config::singleton();
	$user_name = $config->getBotName();
	$user_descr = $config->getBotDescription();
	$user_exitmessage = $config->getExitMessage();
	$user_psw = $config->getPassword();
	$irc_chans = $config->getChans();
	$locale = $config->getLocale(); //"it_IT";
	$is_log_minimal = $config->getMinimalLog();
	$db_engine = $config->getDB();
	$page_prefix = $config->getPagePrefix();
	$page_password = $config->getPagePassword();

	$localedir = "locale/";
	$domain = "messages";
	setlocale(LC_ALL, $locale); //setto la variabile d'ambiente che gettext prende in considerazione
	bindtextdomain($domain, $localedir);
	textdomain($domain);
	bind_textdomain_codeset($domain, "UTF-8"); //indica la codifica dei file di traduzione
	require_once("Internationalizer.class.php");
	$translations = new Internationalizer($locale);

	if($is_log_minimal > 0) {
		require_once("MinimalLogger.class.php");
		$logger = new MinimalLogger($user_name, $irc_chans);
	} else {
		require_once("Logger.class.php");
		$logger = new Logger($user_name, $irc_chans);
	}

	///TODO: Create class for mysqli, sqlite3 and postgres
	require_once("database/{$db_engine}.class.php");
	require_once("database/database.class.php");
	$db = new Database("database.db", "", "", "", "");

	const version = "0.10.9-SNAPSHOT";
	const user_folder = "/dev/shm/channels";
	$chiusura = false;		//When setted to true the Bot will close
	$functions = array();	//array containing information about functions
	$on_join = array();		//array containing information about on_join functions
	$always = array();		//array containing information about functions that must be called on receiving data from socket
	$auth = array();
	$registered = array();

	$parla = array();
	for($i = 0; $i < count($irc_chans); $i++) {
		$users[$irc_chans[$i]] = array();
		$token[$irc_chans[$i]] = false;
		$parla[$irc_chans[$i]] = true;
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

	if(!file_exists(user_folder)) {
		printf(_("directory-notexists-%s") . "\n", user_folder);
		mkdir(user_folder, 0755);
	}

	if($colors == true)
		require_once("shellcoloursenabled.class.php");
	else
		require_once("shellcoloursdisabled.class.php");
	require_once("irccolours.interface.php");
	require_once("common.php");

	echo "\n\n";
	echo ShellColours::BOLD . _("bot-descr") . ShellColours::Z . "\n\n\n";
	echo ShellColours::UNDERLINE . _("conn-summary") . ":" . ShellColours::Z . "\n\n";
	echo _("conn-server") . ":\t\t$irc_server\n";
	echo _("conn-port") . ":\t\t$irc_port\n";
	echo _("conn-channels") . ":\t" . implode(", ", $irc_chans) . "\n";
	echo _("conn-username") . ":\t$user_name\n";
	echo _("conn-desc") . ":\t$user_descr\n";
	echo _("conn-password") . ":\t$user_psw\n";
	echo "\n\n";

	list($functions, $on_join, $always) = getFunctions();

	echo _("socket-create");
	$irc = socket_create(AF_INET, SOCK_STREAM, SOL_TCP);
	$connect = socket_connect($irc, $irc_server, $irc_port);
	if($irc && $connect) {
		echo _("done") . "\n";
	} else {
		echo _("error") . "\n";
		die(socket_strerror(socket_last_error()) . " (" . socket_last_error() . ")");
	}

	echo _("socket-partyline-create");
	$party_mainsck = socket_create(AF_INET, SOCK_STREAM, SOL_TCP);
	if($party_mainsck) {
		echo _("done") . "\n";
		sckdbg($sck_debug, _("socket-partyline-listening") . " $party_addr:$party_port");
		if(!socket_set_option($party_mainsck, SOL_SOCKET, SO_REUSEADDR, 1)) {
			die(socket_strerror(socket_last_error()) . " (" . socket_last_error() . ")");
		}
		socket_bind($party_mainsck, $party_addr, $party_port);
		socket_listen($party_mainsck);
	} else {
		echo _("error") . "\n";
		die(socket_strerror(socket_last_error()) . " (" . socket_last_error() . ")");
	}

	echo "\n\n";
	$pid = pcntl_fork();

	if($pid == -1) {
		die(_("fork-error"));
	} elseif($pid) { //Father
		dbg($debug, _("dbg-user_nick-send"));
		send($irc, "USER $user_name \"1\" \"1\" :$user_descr.\n");
		send($irc, "NICK $user_name\n");
		$party_pid = pcntl_fork();
		if($party_pid == -1) {
			die(_("fork-error"));
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
			pcntl_signal(SIGCHLD, SIG_IGN);
			do {
				$party_sck = socket_accept($party_mainsck);
				socket_getpeername($party_sck, $party_remoteaddress);
				sckdbg($sck_debug, _("socket-partyline-newconn") . " $party_remoteaddress!!");
				$another_socket = party_working($party_sck, $db, $irc, $irc_chans);
				socket_close($party_sck);
			} while($another_socket);
		}
	} else { //Son
		pcntl_signal(SIGTERM, "sig_handler");
		pcntl_signal(SIGHUP,  "sig_handler");
		pcntl_signal(SIGUSR1, "sig_handler");
		pcntl_signal(SIGINT, "sig_handler");
		pcntl_signal(SIGCHLD, SIG_IGN);
		$dirs = getDirs("functions/");
		foreach($dirs as $dir) {
			require_once("functions/{$dir}/init.php");
			call_user_func("{$dir}_init");
		}
		while(!$chiusura) {
			$rawdata = "";
			if(!socket_last_error($irc))
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
					$col = ShellColours::LGREEN;
					$col_ = ShellColours::Z;
				} else {
					$col = $col_ = "";
				}
				$print_timestamp = date("dmYHis");
				echo "[{$print_timestamp}] {$col}<<---   $data{$col_}\n";

				if($type == "001") {
					$myipaddress = end(explode("@", end(explode(" ", $msg))));
				}

				if(isset($slot_saluto[0])) {
					for($i = 0; $i < count($slot_saluto); $i++) {
						if(microtime(true) - $slot_saluto[$i][3] >= 0.05) {
							$slot_saluto[$i][0]--;
							if($slot_saluto[$i][0] == 2) {
								send($irc, "NAMES {$slot_saluto[$i][2]}\n");
								//send($irc, "WHO {$slot_saluto[$i][2]}\n");
							}
						}
					}
				}

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

					file_put_contents(user_folder . "/$chan", implode("\n", $users[$chan]) . "\n");
				}

				if(isset($slot_saluto[0])) {
					if($slot_saluto[0][0] <= 0) {
						$slot_saluto[0][0] = is_user_in_chan($slot_saluto[0][1], $slot_saluto[0][2]);
						if($slot_saluto[0][0] == true) {
							$saluto_user = $slot_saluto[0][1];
							$saluto_chan = $slot_saluto[0][2];
							$new_user = $slot_saluto[0][4];
							$joiner_mode = $db->get_modes($saluto_user, $saluto_chan);
							if($saluta[$saluto_chan]) {
								if($db->cangreet($saluto_user, $saluto_chan) || ($new_user && $salutanuovi[$saluto_chan])) {
									$joiner_mess = $db->get_greet($saluto_user, $saluto_chan);
									sendmsg($irc, sprintf(_("greet-hi-%s"), $saluto_user), $saluto_chan, 0, true);
									if(strlen($joiner_mess) > 0)
										sendmsg($irc, "[$saluto_user]: $joiner_mess", $saluto_chan, 0, true);
									if($new_user)
										sendmsg($irc, sprintf(_("greet-infos-%s-%s"), $user_name, _("command-help")), $saluto_chan, 0, true);
								}
							}
							$mode_len = strlen($joiner_mode);
							if($mode_len > 0) {
								$stringa_mode = "MODE $saluto_chan +$joiner_mode ";
								for($index = 0; $index < $mode_len; $index++)
									$stringa_mode .= $saluto_user . " ";
								send($irc, $stringa_mode . "\n");
								dbg($debug, $stringa_mode);
							}
							foreach($on_join as $join_func) {
								chiama($join_func['folder'], $join_func['name'], $irc, $saluto_chan, $saluto_user, $msg, array("on_join"));
							}
						}
						unset($slot_saluto[0]);
						$slot_saluto = array_values($slot_saluto);
					}
				}

				if($type == "352") { //output di WHO
					$read_users = explode(" ", $msg, 8);
					$who_channel = "#" . $read_users[0];
					$who_user = $read_users[4];
					$who_flags = $read_users[5];
					$registered[$who_user] = preg_match("/r/", $who_flags);
					if(preg_match("/[\+%&$~\@]/", substr($who_flags, -1)))
						$who_user =  substr($who_flags, -1) . $who_user;
					//$users[$who_channel][] = $who_user;
				}

				if($type == "PRIVMSG" && preg_match("/\001PING (.+)\001$/", $msg, $data))
					notice($irc, "\001PING {$data[1]}\001", $sender);

				if(in_array(strtolower($type), array("nick", "quit", "mode", "join", "part"))) {
					if($type == "mode" || $sender != $user_name) {
						send($irc, "NAMES $irc_chan\n");
						send($irc, "WHO $sender\n");
					}
				}
				if(strtolower($type) == "join" && !is_cop($d)) {
					dbg($debug, _("event-newjoin"));
					dbg($debug, "\$joiner = $sender");
					$irc_chan = substr($recv, 1);
					if(strcmp($sender, $user_name) != 0)
						$slot_saluto[] = array(3, $sender, $irc_chan, microtime(true), $db->isnewuser($sender));
				} elseif(($type == "376") || ($type == "422")) {
					dbg($debug, _("event-376-422"));
					if(isset($user_psw) && strlen($user_psw) != 0)
						sendmsg($irc, "IDENTIFY $user_psw", "NickServ");
				} elseif($type == "433") {
					dbg($debug, _("event-433"));
					sendmsg($irc, "GHOST $user_name $user_psw", "NickServ");
					sendmsg($irc, "IDENTIFY $user_psw", "NickServ");
				} elseif(($type == "NOTICE" && $sender == "NickServ" && preg_match("/now recognized|sei riconosciuto/", $msg)) || ($type == "401" && $msg == _("no-nickserv")) || ($type == "NOTICE" && $sender == "NickServ" && preg_match("/not registered|isn't registered|non (.*?)registrato/", $msg))) {
					///TODO: Sistemare queste condizioni!!! Altrimenti funziona solo su un server localizzato in ITA
					foreach($irc_chans as $irc_chan) {
						entra_chan($irc_chan);
					}
				} else {
					//From here all functions
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
						if($cmd == _("command-kill") && is_bot_op($sender) && ($registered[$sender] || $auth[$sender])) { //if($cmd == "sparati" && in_array($sender, $operators) && ($registered[$sender] || $auth[$sender])) {
							sendmsg($irc, _("command-kill-msg1"), $irc_chan, 0, true);
							foreach($irc_chans as $c) {
								if($saluta[$c] == true) {
									sendmsg($irc, _("command-kill-msg2"), $c, 1 / count($irc_chans), true);
									sendmsg($irc, _("command-kill-msg3"), $c, 1 / count($irc_chans), true);
								}
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
								if(preg_match($regex, $cmd, $infos) && is_bot_op($sender) && ($registered[$sender] || $auth[$sender])) {
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
					} elseif(preg_match("/^{$user_name}[ \,;\.:\-!\?]*$/", $msg)) {
						if(rand(1, 10) != 2) {
							$cond_f = array("user_IDUser", "username");
							$cond_o = array("=", "=");
							$cond_v = array("!IDUser!", $sender);

							$result = $db->select(array("poke", "user"), array("poke_message"), array(""), $cond_f, $cond_o, $cond_v, 1, "random");
							if(count($result) == 0)
								sendmsg($irc, sprintf(_("bot-poke-%s"), $sender), $irc_chan);
							else
								sendmsg($irc, $result[0]["poke_message"], $irc_chan);
						} else
							sendmsg($irc, sprintf(_("bot-poke-%s"), $sender), $irc_chan);
					} else {
						foreach($always as $always_func) {
							chiama($always_func['folder'], $always_func['name'], $irc, $irc_chan, $sender, $msg, array("always", $type, $data));
						}
					}
				}
			} else {
				list($type, $msg) = explode(" ", trim($data));
				$print_timestamp = date("dmYHis");
				echo "[{$print_timestamp}] {$col}<<---   $data{$col_}\n";
				if(strtolower($type) == "ping") {
					dbg($debug, _("ping"));
					send($irc, "PONG " . substr($msg, 1) . "\n");
				}
			}
			pcntl_signal_dispatch();
		}
		sendwait($irc, "QUIT {$user_exitmessage}!", 0, true);
		posix_kill(posix_getpid(), SIGTERM);
	}
?>
