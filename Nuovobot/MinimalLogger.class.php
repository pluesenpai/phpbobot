<?php

	require_once("Logger.class.php");

	class MinimalLogger extends Logger
	{
		public function __construct($bot_name, $irc_chans)
		{
			parent::__construct($bot_name, $irc_chans);
		}

		public function logMessage($data, $outgoing = false)
		{
			if($outgoing) //OUTGOING MESSAGE
				$msg = $data;
			else { //INCOMING MESSAGE
				$msg = explode(" ", $data, 2);
				$msg = $msg[1];
			}

			if(preg_match("/^PRIVMSG #|^JOIN [:]*#|^MODE #|^QUIT|^PART/", $msg)) {
				//parent::setLogFile((string)date("Ymd") . ".log");
				parent::logMessage($data, $outgoing);
			}
		}
	}

?>