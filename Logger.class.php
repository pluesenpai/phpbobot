<?php

	class Logger
	{
		private $_folder;
		private $_log_file;
		private $_bot;
		private $_chans;

		public function __construct($bot_name, $irc_chans)
		{
			//$bot_start_date = (string)date("YmdHi");
			//$this->_log_file = "$bot_name-$bot_start_date.log";
			$this->_folder = "logs/";
			$this->_bot = $bot_name;
			$this->_chans = $irc_chans;

			if(!file_exists($this->_folder))
				mkdir($this->_folder, 0755, true);

			foreach($this->_chans as $chan) {
				$file = $this->_folder . substr($chan, 1);
				if(!file_exists($file))
					mkdir($file, 0755, true);
			}
		}

		private function millitime()
		{
			list($usec, $sec) = explode(" ", microtime());
			$microtime = (float)$usec + (float)$sec;

			return(round($microtime * 1000));
		}

		private function files_put_contents($filenames, $data, $flags)
		{
			foreach($filenames as $file)
				file_put_contents($file, $data, $flags);
		}

		private function setLogFile($filename)
		{
			$this->_log_file = $filename;
		}

		public function logMessage($data, $outgoing = false)
		{
			$this->setLogFile((string)date("Ymd") . ".log");
			$timestamp = $this->millitime();

			if($outgoing) { //OUTGOING MESSAGE
				@list($type, $recv, $msg) = explode(" ", $data, min(3, substr_count($data, " ") + 1));
				$paths = array();
				if($recv{0} == "#") //Message to a channel
					$paths[] = $this->_folder . substr($recv, 1) . "/" . $this->_log_file;
				else {
					foreach($this->_chans as $chan)
						$paths[] = $this->_folder . substr($chan, 1) . "/" . $this->_log_file;
				}
				$this->files_put_contents($paths, "$timestamp >>>" . toUTF8($data) . "\n", FILE_APPEND + LOCK_EX);
			} else { //INCOMING MESSAGE
				@list($d, $type, $recv, $msg) = explode(" ", $data, min(4, substr_count($data, " ") + 1));
				$d = substr($d, 1);
				if(strpos($d, "!") !== false) {
					preg_match("/(.*)!.*/", $d, $sender);
					$sender = $sender[1];
				} else
					$sender = $d;
				$paths = array();
				if($recv{0} == "#") //Message to a channel
					$paths[] = $this->_folder . substr($recv, 1) . "/" . $this->_log_file;
				else {
					foreach($this->_chans as $chan)
						$paths[] = $this->_folder . substr($chan, 1) . "/" . $this->_log_file;
				}
				$mess = htmlentities($data, ENT_NOQUOTES, 'UTF-8');
				$newdata = toUTF8($mess);
				$this->files_put_contents($paths, "$timestamp $newdata\n", FILE_APPEND + LOCK_EX);
			}
		}
	}

?>