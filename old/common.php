<?php
	/**
	  * Converts a given text that could contain html
	  * entities into a UTF8 message.
	  * @param $text Text to convert to UTF8
	  * @return UTF8 converted text
	  */
	function toUTF8($text)
	{
		return(html_entity_decode($text, ENT_QUOTES, 'UTF-8'));
	}

	function entra_chan($irc_chan)
	{
		global $irc, $user_name, $token;

		send($irc, "JOIN $irc_chan\n");
		sendmsg($irc, "Ciao a tutti... $user_name &egrave; tornato!", $irc_chan, 0, true);
		sendmsg($irc, "Ora controllo il canale!!!", $irc_chan, 0, true);
		sendmsg($irc, "Per informazioni dai il comando \"$user_name help\"!!!", $irc_chan, 0, true);
		$token[$irc_chan] = true;
	}

	/**
	  * Calls a function from given name in $fun
	  * @param $folder Contains name of folder where is the function
	  * @param $fun The name of the function to call
	  * @param $irc Socket that will be passed to the called function
	  * @param $irc_chan Name of channel that will be passed to the called function
	  * @param $sender Name of sender of the message
	  * @param $infos Other data to pass to the called function
	  */
	function chiama($folder, $fun, $irc, $irc_chan, $sender, $msg, $infos)
	{
		$pid = pcntl_fork();
		if($pid == -1) {
			die("Could not fork");
		} elseif(!$pid) {
			include_once("functions/$folder/$fun.php");
			call_user_func($fun, $irc, $irc_chan, $sender, $msg, $infos);
			if($infos[0] != "always") {
				posix_kill(posix_getppid(), SIGUSR1);
			}
			posix_kill(posix_getpid(), 9);
		}
	}

	/**
	  * Reads a file and returns the content in an array
	  * @param $file
	  * @return returns an array where elements are the rows of $file
	  */
	function get_from_file($file)
	{
		$words_array = preg_replace("#\r\n?|\n#", "", file($file));

		return $words_array;
	}

	/**
	  * From a given filename removes the extension
	  * @param $fileName The name of the file including the extension
	  * @return The name of the file without the extension
	  */
	function removeExtension($fileName)
	{
		$ext = strrchr($fileName, '.');

		if($ext !== false) {
			$fileName = substr($fileName, 0, -strlen($ext));
		}

		return $fileName;
	}

	/**
	  * It scans the dir functions/ for the list of functions.
	  * @return Returns a double array with all information of the functions.
	  */
	function getFunctions()
	{
		$folders = getDirs("functions/");
		$functions = array();
		$on_join = array();
		$always = array();

		$funz = $join = $alw = 0;

		foreach($folders as $folder) {
			$xml = simplexml_load_file("functions/".$folder."/functions.xml");
			foreach($xml->function as $func) {
				if(in_array($func->tipo, array("join", "part", "quit", "nick", "always")) === false) {
					$functions[$funz]['folder'] = $folder;
					$functions[$funz]['name'] = $func->name;
					$functions[$funz]['privileged'] = $func->privileged;
					$functions[$funz]['regex'] = $func->regex;
					$functions[$funz]['descr'] = $func->descr;
					$functions[$funz++]['descr_name'] = $func->descr_name;
				} elseif($func->tipo == "join") {
					$on_join[$join]['folder'] = $folder;
					$on_join[$join++]['name'] = $func->name;
				} elseif($func->tipo == "always") {
					$always[$alw]['folder'] = $folder;
					$always[$alw++]['name'] = $func->name;
				}
			}
		}

		return(array($functions, $on_join, $always));
	}

	/**
	  * Get the list of dirs contained in a folder
	  * @param $dir Folder to scan
	  * @return Returns list of subdirs from given $dir
	  */
	function getDirs($dir)
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

	/**
	  * Get the list of files contained in a folder
	  * @param $dir Folder to scan
	  * @param $type Type of file to retrieve
	  * @return Returns list of files of type $type from the given $dir
	  */
	function getFiles($dir, $type)
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

		return array_values($files);
	}

	/**
	  * Prints to stdout a debug text if $debug is set to true
	  * @param $debug If set to true, then prints debug text to stdout
	  * @param $text Text to print
	  */
	function dbg($debug, $text) {
		global $BLUE, $Z;
		if($debug) {
			echo "{$BLUE} [ deb ] $text{$Z}\n";
		}
	}

	function sckdbg($sck_debug, $text) {	// Questa non te la spiego tanto � facile
		global $LBLUE, $Z;
		if($sck_debug) {
			echo "{$LBLUE} [[sck]] $text{$Z}\n";
		}
	}

	/**
	  * Prints to stderr given text
	  * @param $text Text to print
	  */
	function errecho($text) {
		fwrite(STDERR, "$text\n");
	}

	/**
	  * Sends a message on a socket
	  * @param $stream Socket
	  * @param $data Message to send
	  * @param $delay Number of seconds to wait after sending the message
	  * @return Returns the pid of the son
	  */
	function send($stream, $data, $delay = 0) {
		global $RED, $Z, $logger;
		$pid_write = pcntl_fork();
		$d = $delay;
		if($pid_write == -1) {
			echo "ERROR:  Cannot fork!!!\n";
			die();
		} elseif($pid_write) {
			return $pid_write;
		} elseif(!$pid_write) {
			echo "{$RED}   --->> " . toUTF8($data) . "{$Z}";
			usleep($d * 1000000);
// 			fwrite($stream, $data);
			socket_write($stream, toUTF8($data));
			$logger->logMessage(str_replace(array("\n", "\r"), "", $data), true);
			posix_kill(posix_getpid(), 9);
		}
	}

	/**
	  * Sends a message and eventually waits
	  * @param $stream Socket
	  * @param $msg Message to send
	  * @param $delay Number of seconds to wait after sending the message
	  * @param $wait If set to true waits the called process
	  */
	function sendwait($stream, $msg, $delay = 0, $wait = false)
	{
		$pid = send($stream, "$msg\n", $delay);
		if($wait) {
			pcntl_waitpid($pid, $status);
		}
	}

	/**
	  * Sends a message of type PRIVMSG on a socket
	  * @param $stream Socket
	  * @param $msg Message to send
	  * @param $recv Receiver of the message
	  * @param $delay Number of seconds to wait after sending the message
	  * @param $wait If set to true waits the called process
	  */
	function sendmsg($stream, $msg, $recv, $delay = 0, $wait = false)
	{
		$pid = send($stream, "PRIVMSG $recv :$msg\n", $delay);
		if($wait) {
			pcntl_waitpid($pid, $status);
		}
	}

	/**
	  * Signal Handler
	  * @param $signo Signal number
	  */
	function sig_handler($signo)
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
					call_user_func("{$dir}_update");
				}
				break;
			case SIGCHLD:
				pcntl_waitpid(-1, $status);
			default:
				//handle all other signals
		}
	}

	/**
	  * Prints help of the bot on a private message
	  * @param $irc Socket
	  * @param $sender User to send message to
	  * @param $functions List of functions
	  * @param $short If set to true, shows a summary of the help
	  * @param $folder If setted prints only commands on that specified folder
	  */
	function help($irc, $sender, $functions, $short = false, $folder = "")
	{
		$s = $sender;
		$pid = pcntl_fork();
		if($pid == -1) {
			die("Could not fork");
		} elseif(!$pid) {
			if($folder != "" && !in_array($foler, getDirs("functions/"))) {
				sendmsg($irc, "Spiacente $sender... Non ho nessun groppo chiamato $folder", $s, 1, true);
				return;
			}
			sendmsg($irc, "Ecco la lista delle funzioni:", $s, 1, true);
			if(!$short && ($folder == "" || $folder == "builtins")) {
				$xml = simplexml_load_file("builtins.xml");
				foreach($xml->function as $func) {
					$priv = " ";
					if((int)$func->privileged == 1)
						$priv = "*";
					sendmsg($irc, "($priv) {$func->descr_name}: {$func->descr}", $s, .5, true);
				}
// 				sendmsg($irc, "( ) help: Shows this listing.", $s, 1, true);
// 				sendmsg($irc, "( ) help {GROUP}: Shows functions in that group.", $s, 1, true);
// 				sendmsg($irc, "( ) shorthelp: Shows only groups of available functions.", $s, 1, true);
// 				sendmsg($irc, "( ) ciao: I'll greet all people in this chan.", $s, 1, true);
// 				sendmsg($irc, "( ) saluta: I'll greet all people in this chan.", $s, 1, true);
// 				sendmsg($irc, "( ) saluta {USER}: I'll greet the indicated user.", $s, 1, true);
// 				sendmsg($irc, "( ) salutami: I'll greet you.", $s, 1, true);
// 				sendmsg($irc, "( ) register {PASSWORD}: I'll register you with given password.", $s, 1, true);
// 				sendmsg($irc, "( ) auth {PASSWORD}: I'll authenticate you.", $s, 1, true);
// 				sendmsg($irc, "( ) deauth: I'll deauthenticate you.", $s, 1, true);
// 				sendmsg($irc, "( ) setmessage {MESSAGE}: I'll use the given message for greeting you when you enter in chan where you typed the command.", $s, 1, true);
// 				sendmsg($irc, "( ) delmessage: I'll won't greet you even more.", $s, 1, true);
// 				sendmsg($irc, "(*) sparati: I'll close the connection with this chan.", $s, 1, true);
// 				sendmsg($irc, "(*) debanna {USER}: I'll deban the indicated user.", $s, 1, true);
			}

			if($folder != "builtins") {
				$old = "";
				foreach($functions as $func) {
					if($func['folder'] != $old) {
						$old = $func['folder'];
						if($folder == "" || ($folder != "" && $old == $folder))
							sendmsg($irc, "$old::", $s, .5, true);
					}
					if($folder != "" && $old != $folder) {
						continue;
					}
					if(!$short) {
						$priv = " ";
						if($func['privileged'] == 1)
							$priv = "*";
						sendmsg($irc, "\t\t($priv) {$func['descr_name']}: {$func['descr']}", $s, .5, true);
					}
				}
			}

			if(!$short)
				sendmsg($irc, "     NOTE: (*) means that you need to be bot operator to exec it.", $s);
			posix_kill(posix_getpid(), 9);
		}
	}

	function hide_password($password)
	{
		$arr = str_split($password);
		$ret = array();
		foreach($arr as $char) {
			$ret[] = "*";
		}

		return implode("", $ret);
	}

	function clean_username($username)
	{
		return preg_replace("/^([\+%&$~\@])*(.+)$/", "\$2", $username);
	}

	function combina_array($users)
	{
		$utenti = array();
		foreach($users as $u) {
			$u2 = array_map("clean_username", $u);
			foreach($u2 as $user) {
				if(!in_array($user, $utenti))
					$utenti[] = $user;
			}
		}

		return $utenti;
	}

	function is_bot_op($user)
	{
		global $operators;

		return in_array($user, $operators);
	}

	function is_cop($user)
	{
		list($utente, $d) = split("!", $user);
		if($d == "cop@Security.org")
			return true;
		return false;

	}

	function is_channel_owner($user, $channel)
	{
		global $users;

		return preg_match("/\b(.*)~(.*){$user}\b/", implode(" ", $users[$channel]));
	}

	function is_channel_protected_operator($user, $channel)
	{
		global $users;

		return preg_match("/\b(.*)&(.*){$user}\b/", implode(" ", $users[$channel]));
	}

	function is_user_in_chan($user, $channel)
	{
		global $users;

		return preg_match("/\b(.*){$user}\b/", implode(" ", $users[$channel]));
	}

	function party_working($party_sck, $db, $socket, $irc_chans)
	{
		global $YELLOW, $Z, $user_name;
		$continua = true;	// Questa serve per smettere di ricevere altre connessioni socket
		socket_write($party_sck, "Ciao, per autenticarti digita 'auth NOME PASSWORD'\n");
		$auth = false;
		$channel = "";
		do {
			$data_raw = socket_read($party_sck, 2048, PHP_NORMAL_READ); // Legge sino a \n oppure \r, e comunque non pi� di 2048 byte
			$data = str_replace(array("\n","\r"), "", $data_raw); // Elimina i \n e \r dalla stringa
			$col = $YELLOW;
			$col_ = $Z;
			if($data != "") { // Messo perch� � come se il socket restituisca due righe invece che una, di cui la seconda vuota!!! Da risolvere... Sicuramente roba di buffer da pulire...
				if($data == "exit") {
					continue;
				} elseif($data == "version") {
					socket_write($party_sck, "Ciao, io sono $user_name, versione 1.0\n");
				} elseif(preg_match("/^auth (.+) (.+)$/", $data, $infos) && !$auth) {
					$iduser = $db->verifica_user($infos[1]);
					$auth = $db->verifica_password($iduser, $infos[2]);
					if($auth) {
						socket_write($party_sck, "Sei stato autenticato!\n");
					}
					$data = "auth $infos[1] " . hide_password($infos[2]);
				} elseif($auth == true) {
					if(preg_match("/^channel (.+)$/", $data, $infos)) {
						if(in_array($infos[1], $irc_chans)) {
							$channel = $infos[1];
							socket_write($party_sck, "Canale impostato!\n");
						} else
							socket_write($party_sck, "Non mi trovo in questo canale\n");
					} elseif(preg_match("/^getchannels$/", $data)) {
						socket_write($party_sck, "Lista dei canali in cui mi trovo:\n");
						socket_write($party_sck, implode(", ", $irc_chans) . "\n");
					} elseif($channel != "") {
						sendmsg($socket, $data, $channel);
					} elseif($channel == "") {
						socket_write($party_sck, "Prima seleziona il canale\n");
					}
				}
				echo "{$col} <<sck   $data{$col_}\n"; // Stampa a video i dati ricevuti!!!
				socket_write($party_sck, ($channel != "" ? "[$channel]" : "") . "$ ");
			}
		} while($data != "exit"); // E continua sino a che i dati ricevuti, puliti dal \n o \r, siano diversi da "exit"
		return $continua; // Se scrivi exit e poi invio va al return e ritorna dove � stata chiamata la funzione.
	}
?>