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
				if((string)$chan == $irchan)
					$chan = NULL;
			}

			file_put_contents(self::filename, $this->_xml->asXML());
		}
	}
?>
