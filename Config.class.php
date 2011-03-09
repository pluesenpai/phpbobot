<?php
	/** Class for bot configuration */
	class Config
	{
		const filename = "bot.conf";
		private static $instance;
		private $_xml;

		private $xmlDefaultItems = array(
			"botName" => "bobot",
			"botDescription" => "PHP written Bot for IRC",
			"botExitMessage" => "Byte Byte!!",
			"password+" => "",
			"server" => "irc.syrolnet.org",
			"port" => "6668",
			"chans" => array(
				"chan" => "#bottoli"
			),
			"db" => "pdo_sqlite3",
			"listenAddress" => "127.0.0.1",
			"listenPort" => "25000",
			"locale" => "it_IT",
			"minimallog" => "0",
			"page_prefix" => "",
			"page_password+" => ""
		);

		/**
		 * Trailing + (plus) means field must be encoded
		 */
		private $xmlItems = array(
			"botName" => "*",
			"botDescription" => "*",
			"botExitMessage" => "*",
			"password+" => "*",
			"server" => "*",
			"port" => "*",
			"chans" => array(),
			"db" => "*",
			"listenAddress" => "*",
			"listenPort" => "*",
			"locale" => "*",
			"minimallog" => "*",
			"page_prefix" => "*",
			"page_password+" => "*"
		);

		/**
		  * Constructor for Config Class
		  */
		private function __construct()
		{
			if(!file_exists(self::filename)) {
				touch(self::filename);
			} else {
				$this->_xml = simplexml_load_file(self::filename);
				foreach($this->xmlDefaultItems as $key => $value) {
					if(is_array($value)) {
						$subKey = key($value);
						if(substr($subKey, -1) == "+") {
							$subKey_ = substr($subKey, 0, -1);
							$func = "decode";
						} else {
							$subKey_ = $subKey;
							$func = "dontChange";
						}
						foreach($this->_xml->$key->$subKey_ as $item) {
							$this->xmlItems[$key][] = $this->$func((string)$item);
						}
					} else {
						if(substr($key, -1) == "+") {
							$key_ = substr($key, 0, -1);
							$func = "decode";
						} else {
							$key_ = $key;
							$func = "dontChange";
						}
						$this->xmlItems[$key] = isset($this->_xml->$key_) ? $this->$func((string)$this->_xml->$key_) : "*";
					}
				}
			}

			$changed = false;

			foreach($this->xmlItems as $key => $value) {
				if($value == "*" || (is_array($value) && count($value) == 0)) {
					if(substr($key, -1) == "+") {
						$key_ = substr($key, 0, -1);
						$func = "decode";
					} else {
						$key_ = $key;
						$func = "dontChange";
					}
					$old = $this->xmlDefaultItems[$key];
					if(is_array($value)) {
						$old = implode(" ", $old);
						echo "{$key_}: [$old] ";
						do {
							$subKey = key($value);
							if(substr($subKey, -1) == "+") {
								$subKey_ = ucfirst(substr($subKey, 0, -1));
								$func = "decode";
							} else {
								$subKey_ = ucfirst($subKey);
								$func = "dontChange";
							}
							echo "{$subKey_} (empty to finish): ";
							$new = trim(fgets(STDIN));
							if($new != "") {
								$this->xmlItems[$key][] = $this->$func($new);
								$changed = true;
							}
						} while($new != "");
						if(count($this->xmlItems[$key]) == 0) {
							$this->xmlItems[$key] = $this->xmlDefaultItems[$key];
						}
					} else {
						echo "{$key_}: [$old] ";
						$new = trim(fgets(STDIN));
						if(empty($new)) {
							$new = $old;
						}
						$this->xmlItems[$key] = $new;
						$changed = true;
					}
				}
			}

			if($changed) {
				$this->writeToXml();
				echo "Configuration created!";
			}
		}

		/**
		  * This class cannot be cloned
		  */
		public function __clone()
		{
			trigger_error("Clone is not allowed.", E_USER_ERROR);
		}

		/**
		  * Must be called to create a new instance of this class
		  */
		public static function singleton()
		{
			if(!isset(self::$instance)) {
				$c = __CLASS__;
				self::$instance = new $c;
			}

			return self::$instance;
		}

		private function dontChange($param)
		{
			return $param;
		}

		private function encode($param)
		{
			if($param != "")
				return base64_encode($param);

			return $param;
		}
		
		private function decode($param)
		{
			if($param != "")
				return base64_decode($param);

			return $param;
		}

		private function writeToXml()
		{
			$xml = "<?xml version=\"1.0\" ?>\n<bobot>\n";
			foreach($this->xmlItems as $key => $value) {
				if(substr($key, -1) == "+") {
					$key_ = substr($key, 0, -1);
					$func = "encode";
				} else {
					$key_ = $key;
					$func = "dontChange";
				}
				$xml .= "\t<{$key_}>";
				if(is_array($value)) {
					foreach($value as $k => $item) {
						$k_ = key($this->xmlDefaultItems[$key]);
						if(substr($k_, -1) == "+") {
							$k_ = substr($k_, 0, -1);
							$f = "encode";
						} else {
							$f = "dontChange";
						}
						$xml .= "\n\t\t<{$k_}>" . $this->$func($item) . "</{$k_}>";
					}
					$xml .= "\n\t";
				} else {
					$xml .= $this->$func($value);
				}
				$xml .= "</{$key_}>\n";
			}
			$xml .= "</bobot>\n";
			file_put_contents(self::filename, $xml, LOCK_EX);
		}

		/**
		  * Retrieve the name of the Bot
		  * @returns (String) The name of the Bot
		  */
		public function getBotName()
		{
			return $this->xmlItems["botName"];
		}

		/**
		  * Retrieve the description of the Bot
		  * @returns (String) The description of the Bot
		  */
		public function getBotDescription()
		{
			return $this->xmlItems["botDescription"];
		}

		/**
		  * Retrieve the exit message of the Bot
		  * @returns (String) The exit message of the Bot
		  */
		public function getExitMessage()
		{
			return $this->xmlItems["botExitMessage"];
		}

		/**
		  * Retrieve the password for the Bot
		  * @returns (String) The password for the Bot
		  */
		public function getPassword()
		{
			return $this->xmlItems["password+"];
		}

		/**
		  * Retrieve the server where the Bot must connect
		  * @returns (String) Server address
		  */
		public function getServer()
		{
			return $this->xmlItems["server"];
		}

		/**
		  * Retrieve the port for the Bot
		  * @returns (Int) The port for the Bot
		  */
		public function getPort()
		{
			return (int)$this->xmlItems["port"];
		}

		/**
		  * Retrive the list of chans where bot will enter
		  * @returns (Array of String) List of chans
		  */
		public function getChans()
		{
			return $this->xmlItems["chans"];
		}

		/**
		  * Retrieve the DB Engine
		  * @returns (String) The DB Engine
		  */
		public function getDB()
		{
			return $this->xmlItems["db"];
		}

		/**
		  * Retrieve the port where bot must listen for telnet connections
		  * @returns (Int) The port
		  */
		public function getListenPort()
		{
			return (int)$this->xmlItems["listenPort"];
		}

		/**
		  * Retrieve the listening address for telnet connections
		  * @returns Listening address
		  */
		public function getListenAddress()
		{
			return $this->xmlItems["listenAddress"];
		}

		/**
		  * Retrieve the locale for the Bot
		  * @returns (String) The locale for the Bot
		  */
		public function getLocale()
		{
			return $this->xmlItems["locale"];
		}

		/**
		  * Decides if the bot must create a minimal log
		  * @returns (String) 1 if enabled, 0 if not
		  */
		public function getMinimalLog()
		{
			return (int)$this->xmlItems["minimallog"];
		}

		/**
		  * Retrieve the web page
		  * @returns (String) The web page
		  */
		public function getPagePrefix()
		{
			return (string)$this->xmlItems["page_prefix"];
		}

		/**
		  * Retrieve the password for the web page
		  * @returns (String) The password for the web page
		  */
		public function getPagePassword()
		{
			return $this->xmlItems["page_password+"];
		}
	}
?>
