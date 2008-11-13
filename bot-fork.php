<?php
	system("clear");

	declare(ticks = 1);
	$debug = 1;
	$irc_server = "irc.syrolnet.org";
	$irc_port = 6668;
	$irc_chans = array("#sardylan");
	$user_name = "bobot";
	$user_psw = "E_CHE_LA_VENGO_A_DIRE_A_VOI?!?!?!?!?!";

	$functions = array();

	function toUTF8($text)
	{
		$testo = htmlentities(html_entity_decode($text));

		return(html_entity_decode($testo, ENT_QUOTES, 'UTF-8'));
	}

	function chiama($folder, $fun, $irc, $irc_chan, $infos)
	{
		$pid = pcntl_fork();
		if($pid == -1) {
			die("Could not fork");
		} elseif(!$pid) {
			include_once("functions/$folder/$fun.php");
			call_user_func($fun, $irc, $irc_chan, $infos);
			posix_kill(posix_getppid(), SIGUSR1);
			posix_kill(posix_getpid(), 9);
		}
	}

	function get_from_file($file)
	{
		$words_array = preg_replace("#\r\n?|\n#", "", file($file)); //returns an array where elements are the rows of $file

		return $words_array;
	}

	function removeExtension($fileName)
	{
		$ext = strrchr($fileName, '.');

		if($ext !== false) {
			$fileName = substr($fileName, 0, -strlen($ext));
		}

		return $fileName;
	}

	function getFunctions() //It scans the dirs and retrieves all functions for each folder
	{
		$folders = getDirs("functions/");

		$i = 0;

		foreach($folders as $folder) {
			$xml = simplexml_load_file("functions/".$folder."/functions.xml");
			foreach($xml->function as $func) {
				$j = 0;
				$functions[$i][$j++] = $folder;
				$functions[$i][$j++] = $func->name;
				$functions[$i][$j++] = $func->privileged;
				$functions[$i][$j++] = $func->regex;
				$functions[$i++][$j++] = $func->descr;
			}
		}

		return $functions;
	}

	function getDirs($dir) //Returns list of subdirs from given $dir
	{
		$files = scandir($dir);

		foreach($files as $i => $value) {
			if(substr($value, 0, 1) == '.')           // Removes . and ..
				unset($files[$i]);
			elseif(!is_dir($dir.$value))              // Removes Files
				unset($files[$i]);
		}

		return array_values($files);
	}

	function getFiles($dir, $type) //Returns list of files of type $type from the given $dir
	{
		$files = scandir($dir);
		$type_len = strlen($type);

		foreach($files as $i => $value) {
			if(substr($value, 0, 1) == '.')             // Removes . and ..
				unset($files[$i]);
			elseif(is_dir($dir.$value))                 // Removes Directories
				unset($files[$i]);
			elseif(substr($value, -1, 1) == '~')        // Removes Backup Files
				unset($files[$i]);
			elseif(substr($value, -$type_len, $type_len) != $type)  //Removes files where extension is different from $type
				unset($files[$i]);
		}

		return array_values($files); //Restoring array indices
	}

	function dbg($debug, $text) {
		if($debug) {
			echo " [ deb ] $text\n";
		}
	}

	function send($stream, $data, $delay = 0) {
		$pid_write = pcntl_fork();
		$d = $delay;
		if($pid_write == -1) {
			echo "ERROR:  Cannot fork!!!\n";
			die();
		} elseif($pid_write) {
			return true;
		} elseif(!$pid_write) {
			echo "   --->> $data";
			usleep($d * 1000000);
			fwrite($stream, $data);
			posix_kill(posix_getpid(), 9);
		}
	}

	function sendmsg($stream, $msg, $recv, $delay = 0, $wait = false)
	{
		$message = toUTF8($msg);
		send($stream, "PRIVMSG $recv :$message\n", $delay);
		if($wait) {
			pcntl_wait(&$status);
		}
	}

	function sig_handler($signo) //Signal handler
	{
		switch($signo) {
			case SIGTERM:
			case SIGHUP:
				global $chiusura; //It's global because $chiusura is extern to the function
				$chiusura = true;
				break;
			case SIGUSR1:
				$dirs = getDirs("functions/");
				foreach($dirs as $dir) {
					echo "Calling {$dir}_update\n";
					call_user_func("{$dir}_update");
				}
				break;
			default:
				//handle all other signals
		}
	}

	function help($irc, $sender, $functions)
	{
		$s = $sender;
		$pid = pcntl_fork();
		if($pid == -1) {
			die("Could not fork");
		} elseif(!$pid) {
			sendmsg($irc, "Ecco la lista delle funzioni:", $s, 1, true);
			sendmsg($irc, "( ) help: Shows this listing.", $s, 1, true);
			sendmsg($irc, "( ) ciao: I'll greet all people in this chan.\n", $s, 1, true);
			sendmsg($irc, "( ) saluta: I'll greet all people in this chan.\n", $s, 1, true);
			sendmsg($irc, "( ) salutami: I'll greet you.", $s, 1, true);
			sendmsg($irc, "(*) sparati: I'll close the connection with this chan.\n", $s, 1, true);
			sendmsg($irc, "(*) debanna: I'll deban the user indicated.\n", $s, 1, true);

			foreach($functions as $func) {
				$priv = " ";
				if($func[2] == 1)
					$priv = "*";
				sendmsg($irc, "($priv) $func[1]: $func[4]\n", $s, 1, true);
			}
			sendmsg($irc, "     NOTE: (*) means that you need to be bot operator to exec it.", $s);
			posix_kill(posix_getpid(), 9);
		}
	}

	echo "\n\n";
	echo "roBOT for IRC Network\n\n\n";
	echo "Summary of connection data:\n\n";
	echo "Server:\t\t$irc_server\n";
	echo "Port:\t\t$irc_port\n";
	echo "Channel:\t$irc_chan\n";
	echo "UserName:\t$user_name\n";
	echo "Password:\t$user_psw\n";
	echo "\n\n";

	$chiusura = false; //When setted to true the Bot will close
	$functions = getFunctions();

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
			include_once("functions/$func[0]/init.php");
		while (!feof($irc) && $chiusura == false) {
			$rawdata = str_replace(array("\n","\r"), "", fgets($irc, 512));
			$data = trim(str_replace("  ", " ", $rawdata));
			echo " <<---   $data\n";
			if($data[0] === ":") {
				list($d, $type, $recv, $msg) = explode(" ", trim($data), 4);
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
					///NOTE: "/^filetor[ \,;.:\-!\?]*[ ]+(.*)$/" Questa e' la regex completa... andrebbe rimossa la divisione di $data in $d[]
     				///NOTE: Eliminata divisione di $data in $d[], quindi ho cambiato la vecchia regex: /^{$user_name}[ \,;.:\-!\?]*$/
					if(preg_match("/^{$user_name}[ \,;.:\-!\?]*[ ]+(.*)$/", $msg, $ret)) { // Accetta tutti i $user_name + una combinazione (lunga quanto vuoi) dei char nelle [ ]...
						if(count($ret) > 1) {
							unset($ret[0]);
							$ret = array_values($ret);
							$cmd = implode(" ", $ret);
							/// TODO: eregiare anche il $d[4]...
							dbg($debug, "\$cmd = $cmd");
							if($cmd == "salutami") {
								sendmsg($irc, "Ciao $sender :)", $irc_chan);
							}
							if($cmd == "saluta") {
								sendmsg($irc, "Ciao a tutti!! :)", $irc_chan);
							}
							if(preg_match("/^saluta (.*)$/", $cmd, $name)) {
								sendmsg($irc, "Ciao $name[1]... Come stai??", $irc_chan);
								/// TODO: $d[5] va controllato se e' in chan... Altrimenti stai salutando il nulla ;)
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
								$folder = $functions[$i][0];
								$fun = $functions[$i][1];
								$priv = $functions[$i][2];
								$regex = $functions[$i][3];
								if($priv == 1) {
									if(preg_match($regex, $cmd, $infos) && in_array($sender, $operators)) {
										chiama($folder, $fun, $irc, $irc_chan, $infos);
										$trovato = true;
									}
								} else {
									if(preg_match($regex, $cmd, $infos)) {
										chiama($folder, $fun, $irc, $irc_chan, $infos);
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
						} else {
							sendmsg($irc, "Cosa c'&egrave; $sender??", $irc_chan);
						}
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
