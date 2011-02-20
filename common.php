<?php
	class UserLevels
	{
		const OWNER_LEVEL = 0;
		const PROTECTED_LEVEL = 1;
		const OPER_LEVEL = 2;
		const HALFOP_LEVEL = 3;
		const VOICE_LEVEL = 4;
		const NONE_LEVEL = 5;
	}

	/**
	  * Converts a given text that could contain html
	  * entities into a UTF8 message.
	  * @param $text Text to convert to UTF8
	  * @return UTF8 converted text
	  */
	function toUTF8($text, $quotes = ENT_QUOTES)
	{
		return(html_entity_decode($text, $quotes, "UTF-8"));
	}

	function entra_chan($irc_chan)
	{
		global $irc, $user_name, $token, $saluta;

		send($irc, "JOIN $irc_chan\n");
		if($saluta[$irc_chan] == true) {
			sendmsg($irc, "Ciao a tutti... $user_name &egrave; tornato!", $irc_chan, 0, true);
			sendmsg($irc, "Per informazioni dai il comando \"$user_name help\"!!!", $irc_chan, 0, true);
			me($irc, "Ora controlla il canale!!!", $irc_chan, 0, true);
		}
		$token[$irc_chan] = true;
		send($irc, "WHO {$irc_chan}\n");
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
				pcntl_signal_dispatch();
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
		$ext = strrchr($fileName, ".");

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
		global $locale;
		$folders = getDirs("functions/");
		$functions = array();
		$on_join = array();
		$always = array();

		$funz = $join = $alw = 0;

		foreach($folders as $folder) {
			$basename = "functions/$folder/functions.xml";
			if(file_exists($basename))
				$xml = simplexml_load_file($basename);
			else {
				require_once("$basename.php");
				$xml = simplexml_load_string(call_user_func("{$folder}_generateXml", $locale));
			}
			foreach($xml->function as $func) {
				if(in_array((string)$func->tipo, array("join", "part", "quit", "nick", "always")) === false) {
					$functions[$funz]["folder"] = (string)$folder;
					$functions[$funz]["name"] = (string)$func->name;
					$functions[$funz]["privileged"] = (string)$func->privileged;
					$functions[$funz]["regex"] = (string)$func->regex;
					$functions[$funz]["descr"] = (string)$func->descr;
					$functions[$funz++]["descr_name"] = (string)$func->descr_name;
				} elseif($func->tipo == "join") {
					$on_join[$join]["folder"] = (string)$folder;
					$on_join[$join++]["name"] = (string)$func->name;
				} elseif($func->tipo == "always") {
					$always[$alw]["folder"] = (string)$folder;
					$always[$alw++]["name"] = (string)$func->name;
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
			if(substr($value, 0, 1) == ".")           // Removes . and ..
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
			if(substr($value, 0, 1) == ".")             // Removes . and ..
				unset($files[$i]);
			elseif(is_dir($dir.$value))                 // Removes Directories
				unset($files[$i]);
			elseif(substr($value, -1, 1) == "~")        // Removes Backup Files
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
		if($debug)
			echo ShellColours::BLUE . " [ deb ] $text" . ShellColours::Z . "\n";
	}

	function sckdbg($sck_debug, $text) {	// Questa non te la spiego tanto è facile
		if($sck_debug)
			echo ShellColours::LBLUE . " [[sck]] $text" . ShellColours::Z . "\n";
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
		global $logger, $parla, $irc_chans;

		$channel = array_slice(explode(" ", $data), 1, 1);
		if(count($channel) > 0 && in_array($channel[0], $irc_chans) && $parla[$channel[0]] == false) {
			return;
		}
		$pid_write = pcntl_fork();
		$d = $delay;
		if($pid_write == -1) {
			echo "ERROR:  Cannot fork!!!\n";
			die();
		} elseif($pid_write) {
			return $pid_write;
		} elseif(!$pid_write) {
			$print_timestamp = date("dmYHis");
			echo "[{$print_timestamp}] " . ShellColours::RED . "  --->> " . toUTF8($data) . ShellColours::Z;
			usleep($d * 1000000);
// 			fwrite($stream, $data);
			socket_write($stream, toUTF8($data, ENT_NOQUOTES));
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
	  * Sends an action to channel
	  * @param $stream Socket
	  * @param $msg Message to send
	  * @param $recv Receiver of the message
	  * @param $delay Number of seconds to wait after sending the message
	  * @param $wait If set to true waits the called process
	  */
	function me($stream, $msg, $recv, $delay = 0, $wait = false)
	{
		$pid = send($stream, "PRIVMSG $recv :\001ACTION $msg\001\n", $delay);
		if($wait) {
			pcntl_waitpid($pid, $status);
		}
	}

	/**
	  * Sends a notice to user
	  * @param $stream Socket
	  * @param $msg Message to send
	  * @param $recv Receiver of the message
	  * @param $delay Number of seconds to wait after sending the message
	  * @param $wait If set to true waits the called process
	  */
	function notice($stream, $msg, $recv, $delay = 0, $wait = false)
	{
		$pid = send($stream, "NOTICE $recv :$msg\n", $delay);
		if($wait) {
			pcntl_waitpid($pid, $status);
		}
	}

	/**
	  * Sends a notice to user
	  * @param $stream Socket
	  * @param $msg Message to send
	  * @param $recv Receiver of the message
	  * @param $delay Number of seconds to wait after sending the message
	  * @param $wait If set to true waits the called process
	  * @param $going If set to true goes away.
	  */
	function away($stream, $msg, $going = true, $delay = 0, $wait = false)
	{
		if($going)
			$pid = send($stream, "AWAY :$msg\n", $delay);
		else
			$pid = send($stream, "AWAY\n", $delay);

		if($wait) {
			pcntl_waitpid($pid, $status);
		}
	}

	function getpage($url)
	{
		$result = explode("/", $url);
		$host = $result[0];
		if($host == "http:")
			$host = $result[2];

		$page = curl_init();
		$user_agent = "Mozilla/5.0 (X11; U; Linux i686; it; rv:1.9.1.2) Gecko/20090729 Firefox/3.5.2";
		$header = array(
			"Host: {$host}",
			"Accept: text/html,application/xhtml+xml,application/xml;q=0.9,*/*;q=0.8",
			"Accept-Language: it-it,it;q=0.8,en-us;q=0.5,en;q=0.3",
			"Accept-Charset: UTF-8,*",
			"Keep-Alive: 300",
			"Connection: keep-alive"
		);

		curl_setopt($page, CURLOPT_URL, $url);
		curl_setopt($page, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($page, CURLOPT_BINARYTRANSFER, true);
		curl_setopt($page, CURLOPT_HEADER, false);
		curl_setopt($page, CURLOPT_USERAGENT, $user_agent);
		curl_setopt($page, CURLOPT_HTTPHEADER, $header);

		$content = curl_exec($page);
		curl_close($page);

		//print_r($content);

		return $content;
	}

	/**
	  * Signal Handler
	  * @param $signo Signal number
	  */
	function sig_handler($signo)
	{
		switch($signo) {
			case SIGTERM:
			case SIGINT:
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
// 	function help($irc, $sender, $functions, $short = false, $folder = "")
// 	{
// 		$s = $sender;
// 		$pid = pcntl_fork();
// 		if($pid == -1) {
// 			die("Could not fork");
// 		} elseif(!$pid) {
// 			if($folder != "" && $folder != "builtins" && !in_array($folder, getDirs("functions/"))) {
// 				sendmsg($irc, "Spiacente $sender... Non ho nessun gruppo chiamato $folder", $s, 1, true);
// 				return;
// 			}
// 			sendmsg($irc, "Ecco la lista delle funzioni:", $s, 1, true);
// 			if(!$short && ($folder == "" || $folder == "builtins")) {
// 				$xml = simplexml_load_file("builtins.xml");
// 				foreach($xml->function as $func) {
// 					$priv = " ";
// 					if((int)$func->privileged == 1)
// 						$priv = "*";
// 					sendmsg($irc, "($priv) {$func->descr_name}: {$func->descr}", $s, .5, true);
// 				}
// 			}
// 
// 			if($folder != "builtins") {
// 				$old = "";
// 				foreach($functions as $func) {
// 					if($func["folder"] != $old) {
// 						$old = $func["folder"];
// 						if($folder == "" || ($folder != "" && $old == $folder))
// 							sendmsg($irc, "$old::", $s, .5, true);
// 					}
// 					if($folder != "" && $old != $folder) {
// 						continue;
// 					}
// 					if(!$short) {
// 						$priv = " ";
// 						if($func["privileged"] == 1)
// 							$priv = "*";
// 						sendmsg($irc, "\t\t($priv) {$func["descr_name"]}: {$func["descr"]}", $s, .5, true);
// 					}
// 				}
// 			}
// 
// 			if(!$short)
// 				sendmsg($irc, "     NOTE: (*) means that you need to be bot operator to exec it.", $s);
// 			posix_kill(posix_getpid(), 9);
// 		}
// 	}

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
			foreach($u as $user) {
				if(!in_array($user, $utenti))
					$utenti[] = $user;
			}
		}

		return $utenti;
	}

	function is_bot_op($user)
	{
		global $operators;

		return in_array(clean_username($user), $operators);
	}

	function is_cop($user)
	{
		list($utente, $d) = preg_split("/!/", $user);
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

	function is_channel_operator($user, $channel)
	{
		global $users;

		return preg_match("/\b(.*)@(.*){$user}\b/", implode(" ", $users[$channel]));
	}

	function is_channel_half_operator($user, $channel)
	{
		global $users;

		return preg_match("/\b(.*)%(.*){$user}\b/", implode(" ", $users[$channel]));
	}

	function is_channel_voice_user($user, $channel)
	{
		global $users;

		return preg_match("/\b(.*)+(.*){$user}\b/", implode(" ", $users[$channel]));
	}

	function getUserPrivileges($user, $channel)
	{
		if(is_channel_owner($user, $channel))
			return UserLevels::OWNER_LEVEL;
		elseif(is_channel_protected_operator($user, $channel))
			return UserLevels::PROTECTED_LEVEL;
		elseif(is_channel_operator($user, $channel))
			return UserLevels::OPER_LEVEL;
		elseif(is_channel_half_operator($user, $channel))
			return UserLevels::HALFOP_LEVEL;
		elseif(is_channel_voice_user($user, $channel))
			return UserLevels::VOICE_LEVEL;
		else
			return UserLevels::NONE_LEVEL;
	}

	function is_user_in_chan($user, $channel)
	{
		global $users;

		return preg_match("/\b(.*){$user}\b/", implode(" ", $users[$channel]));
	}

	function str_esc($stringa)
	{
		if(get_magic_quotes_gpc())
			return $stringa;
		else
			return addslashes($stringa);
	}

	function party_working($party_sck, $db, $socket, $irc_chans)
	{
		global $user_name;
		$continua = true;	// Questa serve per smettere di ricevere altre connessioni socket
		socket_write($party_sck, "Ciao, per autenticarti digita 'auth NOME PASSWORD'\n");
		$auth = false;
		$channel = "";
		do {
			$data_raw = socket_read($party_sck, 2048, PHP_NORMAL_READ); // Legge sino a \n oppure \r, e comunque non più di 2048 byte
			$data = str_replace(array("\n","\r"), "", $data_raw); // Elimina i \n e \r dalla stringa
			if($data != "") { // Messo perché è come se il socket restituisca due righe invece che una, di cui la seconda vuota!!! Da risolvere... Sicuramente roba di buffer da pulire...
				if($data == "exit") {
					continue;
				} elseif($data == "version") {
					socket_write($party_sck, "Ciao, io sono $user_name, versione " . version . "\n");
				} elseif(preg_match("/^auth (.+) (.+)$/", $data, $infos) && !$auth) {
					//$iduser = $db->check_user($infos[1]);
					$auth = $db->check_password($infos[1], $infos[2]);
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
				echo ShellColours::YELLOW . " <<sck   $data" . ShellColours::Z . "\n"; // Stampa a video i dati ricevuti!!!
				socket_write($party_sck, ($channel != "" ? "[$channel]" : "") . "$ ");
			}
		} while($data != "exit"); // E continua sino a che i dati ricevuti, puliti dal \n o \r, siano diversi da "exit"
		return $continua; // Se scrivi exit e poi invio va al return e ritorna dove è stata chiamata la funzione.
	}

	function callpage($action, $params)
	{
		global $page_prefix, $page_password;

		if($page_prefix != "") {
			$page = "{$page_prefix}?action={$action}&psw=" . md5($page_password);
			foreach($params as $key => $value) {
				$page .= "&{$key}={$value}";
			}
			getpage($page);
		}
	}

?>
