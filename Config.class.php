<?php
	/** Class for bot configuration */
	class Config
	{
		const filename = "bot.conf";
		private static $instance;
		private $_xml;

		/**
		  * Constructor for Config Class
		  */
		private function __construct()
		{
			if(!file_exists(self::filename)) {
				copy(self::filename . ".default", self::filename);

				$this->_xml = simplexml_load_file(self::filename);

				//Now ask for new configuration data
				$oldname = $this->getBotName();
				echo "Bot name: [$oldname] ";
				$name = trim(fgets(STDIN));
				if($name != "")
					$this->setBotName($name);

				$olddescr = $this->getBotDescription();
				echo "Bot description: [$olddescr] ";
				$description = trim(fgets(STDIN));
				if($description != "")
					$this->setBotDescription($description);

				$oldexitmessage = $this->getExitMessage();
				echo "Bot exit message: [$oldexitmessage] ";
				$exitmessage = trim(fgets(STDIN));
				if($exitmessage != "")
					$this->setExitMessage($exitmessage);

				echo "Bot password: ";
				$password = trim(fgets(STDIN));
				$this->setPassword($password);

				$oldaddress = $this->getServer();
				echo "Server address: [$oldaddress] ";
				$address = trim(fgets(STDIN));
				if($address != "")
					$this->setServer($address);

				$oldport = $this->getPort();
				echo "Server port: [$oldport] ";
				$port = trim(fgets(STDIN));
				if($port != "")
					$this->setPort($port);

				$this->removeChan("#sardylan");
				$this->removeChan("#bottoli");
				do {
					echo "Channel (empty to finish): ";
					$channel = trim(fgets(STDIN));
					if($channel != "")
						$this->addChans($channel);
				} while($channel != "");

				$old_db = $this->getDB();
				echo "Database: [$old_db] ";
				$db = trim(fgets(STDIN));
				if($db != "")
					$this->setDB($db);

				$old_laddress = $this->getListenAddress();
				echo "Listening address: [$old_laddress] ";
				$l_address = trim(fgets(STDIN));
				if($l_address != "")
					$this->setListenAddress($l_address);

				$old_lport = $this->getListenPort();
				echo "Listening port: [$old_lport]";
				$l_port = trim(fgets(STDIN));
				if($l_port != "")
					$this->setListenPort($l_port);

				$old_locale = $this->getLocale();
				echo "Locale: [$old_locale]";
				$locale = trim(fgets(STDIN));
				if($locale != "")
					$this->setBotLocale($locale);

				$old_minimallog = $this->getMinimalLog();
				echo "Database: [$old_minimallog] ";
				$minimallog = trim(fgets(STDIN));
				if($minimallog != "")
					$this->setMinimalLog($minimallog);

				echo "Configuration created!";
			}
			$this->_xml = simplexml_load_file(self::filename);
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

		/**
		  * Retrieve the name of the Bot
		  * @returns (String) The name of the Bot
		  */
		public function getBotName()
		{
			return (string)$this->_xml->botName;
		}

		/**
		  * Permits to set the name of the Bot
		  * @param $name New name of the bot
		  */
		public function setBotName($name)
		{
			$this->_xml->botName = $name;
			file_put_contents(self::filename, $this->_xml->asXML());
		}

		/**
		  * Retrieve the description of the Bot
		  * @returns (String) The description of the Bot
		  */
		public function getBotDescription()
		{
			return (string)$this->_xml->botDescription;
		}

		/**
		  * Permits to set the description of the Bot
		  * @param $name New description of the bot
		  */
		public function setBotDescription($descr)
		{
			$this->_xml->botDescription = $descr;
			file_put_contents(self::filename, $this->_xml->asXML());
		}

		/**
		  * Retrieve the exit message of the Bot
		  * @returns (String) The exit message of the Bot
		  */
		public function getExitMessage()
		{
			return (string)$this->_xml->botExitMessage;
		}

		/**
		  * Permits to set the exit message of the Bot
		  * @param $message New exit message of the bot
		  */
		public function setExitMessage($message)
		{
			$this->_xml->botExitMessage = $message;
			file_put_contents(self::filename, $this->_xml->asXML());
		}

		/**
		  * Retrieve the password for the Bot
		  * @returns (String) The password for the Bot
		  */
		public function getPassword()
		{
			return base64_decode((string)$this->_xml->password);
		}

		/**
		  * Permits to set the password of the Bot
		  * @param $password New password of the bot
		  */
		public function setPassword($password)
		{
			$this->_xml->password = base64_encode($password);
			file_put_contents(self::filename, $this->_xml->asXML());
		}

		/**
		  * Retrieve the server where the Bot must connect
		  * @returns (String) Server address
		  */
		public function getServer()
		{
			return (string)$this->_xml->server;
		}

		/**
		  * Permits to set the server where the Bot must connect
		  * @param $server New server address
		  */
		public function setServer($server)
		{
			$this->_xml->server = $server;
			file_put_contents(self::filename, $this->_xml->asXML());
		}


		/**
		  * Retrieve the port for the Bot
		  * @returns (Int) The port for the Bot
		  */
		public function getPort()
		{
			return (int)$this->_xml->port;
		}

		/**
		  * Permits to set the port of the Bot
		  * @param $port New port of the bot
		  */
		public function setPort($port)
		{
			$this->_xml->port = $port;
			file_put_contents(self::filename, $this->_xml->asXML());
		}

		/**
		  * Retrive the list of chans where bot will enter
		  * @returns (Array of String) List of chans
		  */
		public function getChans()
		{
			$chans = array();
			foreach($this->_xml->chans->chan as $chan) {
				$chans[] = (string)$chan;
			}

			return $chans;
		}

		/**
		  * Permits to add new chans to the configuration
		  * @note You can put how many arguments you want.
		  */
		public function addChans()
		{
			$n_args = func_num_args();
			$args = func_get_args();

			for($i = 0; $i < $n_args; $i++) {
				$this->_xml->chans->addChild("chan", $args[$i]);
			}

			file_put_contents(self::filename, $this->_xml->asXML());
		}

		/**
		  * Permits to delete a chan from the configuration
		  * @param $ircchan Chan to be deleted
		  */
		public function removeChan($ircchan)
		{
			foreach($this->_xml->chans->chan as $chan) {
				if((string)$chan == $ircchan) {
					$dom = dom_import_simplexml($chan);
					$dom->parentNode->removeChild($dom);
				}
			}

			file_put_contents(self::filename, $this->_xml->asXML());
		}


		/**
		  * Retrieve the DB Engine
		  * @returns (String) The DB Engine
		  */
		public function getDB()
		{
			return (string)$this->_xml->db;
		}

		/**
		  * Permits to set the DB Engine of the bot
		  * @param $port New DB Engine
		  */
		public function setDB($db)
		{
			$this->_xml->db = $db;
			file_put_contents(self::filename, $this->_xml->asXML());
		}

		/**
		  * Retrieve the port where bot must listen for telnet connections
		  * @returns (Int) The port
		  */
		public function getListenPort()
		{
			return (int)$this->_xml->listenPort;
		}

		/**
		  * Permits to set the port where bot must listen for telnet connections
		  * @param $port New port
		  */
		public function setListenPort($port)
		{
			$this->_xml->listenPort = $port;
			file_put_contents(self::filename, $this->_xml->asXML());
		}

		/**
		  * Retrieve the listening address for telnet connections
		  * @returns Listening address
		  */
		public function getListenAddress()
		{
			return (string)$this->_xml->listenAddress;
		}

		/**
		  * Permits to set the listening address for telnet connections
		  * @param $address New address
		  */
		public function setListenAddress($address)
		{
			$this->_xml->listenAddress = $address;
			file_put_contents(self::filename, $this->_xml->asXML());
		}

		/**
		  * Retrieve the locale for the Bot
		  * @returns (String) The locale for the Bot
		  */
		public function getLocale()
		{
			return (string)$this->_xml->locale;
		}

		/**
		  * Permits to set the locale of the Bot
		  * @param $locale New locale of the bot
		  */
		public function setBotLocale($locale)
		{
			$this->_xml->locale = $locale;
			file_put_contents(self::filename, $this->_xml->asXML());
		}

		/**
		  * Retrieve the locale for the Bot
		  * @returns (String) The locale for the Bot
		  */
		public function getMinimalLog()
		{
			return (int)$this->_xml->minimallog;
		}

		/**
		  * Permits to set the locale of the Bot
		  * @param $locale New locale of the bot
		  */
		public function setMinimalLog($minimallog = 1)
		{
			$this->_xml->minimallog = (int)$minimallog;
			file_put_contents(self::filename, $this->_xml->asXML());
		}

		/**
		  * Retrieve the web page
		  * @returns (String) The web page
		  */
		public function getPagePrefix()
		{
			return (string)$this->_xml->page_prefix;
		}

		/**
		  * Permits to set web page
		  * @param $password New web page
		  */
		public function setPagePrefix($page)
		{
			$this->_xml->page_prefix = $page;
			file_put_contents(self::filename, $this->_xml->asXML());
		}

		/**
		  * Retrieve the password for the web page
		  * @returns (String) The password for the web page
		  */
		public function getPagePassword()
		{
			return base64_decode((string)$this->_xml->page_password);
		}

		/**
		  * Permits to set the password of the web page
		  * @param $password New password of the web page
		  */
		public function setPagePassword($password)
		{
			$this->_xml->page_password = base64_encode($password);
			file_put_contents(self::filename, $this->_xml->asXML());
		}

	}
?>
