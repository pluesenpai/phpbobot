<?php
	/**
	  * Converts a given text that could contain html
	  * entities into a UTF8 message.
	  * @param $text Text to convert to UTF8
	  * @return UTF8 converted text
	  */
	function toUTF8($text)
	{
		$testo = htmlentities(html_entity_decode($text));

		return(html_entity_decode($testo, ENT_QUOTES, 'UTF-8'));
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
			posix_kill(posix_getppid(), SIGUSR1);
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
		global $BOLD, $Z;
		if($debug) {
			echo "{$BLUE} [ deb ] $text{$Z}\n";
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
		global $RED, $Z;
		$pid_write = pcntl_fork();
		$d = $delay;
		if($pid_write == -1) {
			echo "ERROR:  Cannot fork!!!\n";
			die();
		} elseif($pid_write) {
			return $pid_write;
		} elseif(!$pid_write) {
			echo "{$RED}   --->> $data{$Z}";
			usleep($d * 1000000);
			fwrite($stream, $data);
			posix_kill(posix_getpid(), 9);
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
		$message = toUTF8($msg);
		$pid = send($stream, "PRIVMSG $recv :$message\n", $delay);
		if($wait) {
			pcntl_wait($pid, &$status);
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
			default:
				//handle all other signals
		}
	}

	/**
	  * Prints help of the bot on a private message
	  * @param $irc Socket
	  * @param $sender User to send message to
	  * @param $functions List of functions
	  */
	function help($irc, $sender, $functions, $color)
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
			sendmsg($irc, "( ) saluta {USER}: I'll greet the indicated user.\n", $s, 1, true);
			sendmsg($irc, "( ) salutami: I'll greet you.", $s, 1, true);
			sendmsg($irc, "(*) sparati: I'll close the connection with this chan.\n", $s, 1, true);
			sendmsg($irc, "(*) debanna {USER}: I'll deban the indicated user.\n", $s, 1, true);

			$old = "";
			foreach($functions as $func) {
				if($func['folder'] != $old) {
					$old = $func['folder'];
					sendmsg($irc, $old, $s);
				}
				$priv = " ";
				if($func['privileged'] == 1)
					$priv = "*";
				sendmsg($irc, "\t\t($priv) {$func['descr_name']}: {$func['descr']}", $s, 1, true);
			}
			sendmsg($irc, "     NOTE: (*) means that you need to be bot operator to exec it.", $s);
			posix_kill(posix_getpid(), 9);
		}
	}
?>