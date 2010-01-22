<?php
	/** Class for database operations of the bot */
	class Database extends DBHandler
	{
		/**
		  * Constructor of the class
		  *
		  * @param $dbname {STRING}  Name of database
		  * @param $dbhost {STRING}  Host to connect to
		  * @param $dbport {INTEGER} Port to connect to
		  * @param $dbuser {STRING}  User of the database
		  * @param $dbpass {STRING}  Password of the database
		  */
		function __construct($dbname, $dbhost, $dbport, $dbuser, $dbpass)
		{
			parent::__construct($dbname, $dbhost, $dbport, $dbuser, $dbpass);
		}

		/**
		  * Destructor of the class
		  */
		function __destruct()
		{
			parent::__destruct();
		}

		/**
		  * Checks if user is present in Bot's DataBase
		  *
		  * @param $user {STRING} Name of the user
		  *
		  * @returns {INTEGER} ID number of user or 0 if not present
		  */
		function find_user($user)
		{
			$result = $this->select(array("user"), array("IDUser"), array(""), array("username"), array("="), array($user));

			return (count($result) > 0) ? (int)$result[0]['IDUser'] : 0;
		}

		/**
		  * Checks if channel name is present in Bot's DataBase
		  *
		  * @param $chan {STRING} Name of the channel
		  *
		  * @returns {INTEGER} ID number of channel or 0 if not present
		  */
		function find_chan($chan)
		{
			$result = $this->select(array("chan"), array("IDChan"), array(""), array("name"), array("="), array($chan));

			return (count($result) > 0) ? (int)$result[0]['IDChan'] : 0;
		}

		/**
		  * Permits to add a user to the database if not exists
		  * with password, greeting message and modes
		  *
		  * @param $user     {STRING}               User name
		  * @param $chan     {STRING}               Channel
		  * @param $password {STRING} [DEFAULT: ""] Password
		  * @param $mess     {STRING} [DEFAULT: ""] Greeting message
		  * @param $modes    {STRING} [DEFAULT: ""] Modes to assign
		  *
		  * @returns {INTEGER} ID number of user
		  */
		function add_user($user, $chan, $password = "", $mess = "", $modes = "")
		{
			$iduser = $this->check_user($user, $password);
			$idchan = $this->check_chan($chan);

			if($mess != "") {
				$idsaluto = $this->check_greet($mess);
				$this->check_enter($iduser, $idchan, $idsaluto, $modes);
			}
		}

		/**
		  * Similar to find_user() but if user is not present, then add it to database
		  * without password, greeting message or modes
		  *
		  * @param $user     {STRING}               Name of the user
		  * @param $password {STRING} [DEFAULT: ""] Optional password of the user
		  *
		  * @returns {INTEGER} ID number of user
		  */
		function check_user($user, $password = "")
		{
			if($password != "")
				$psw = md5($password);
			else
				$psw = "";

			$iduser = $this->find_user($user);
			if($iduser == 0) {
				///TODO: Aggiungere alla configurazione i bot_op
				if(in_array($user, array('paolo86', 'sardylan')))
					$op = "true";
				else
					$op = "false";

				return $this->insert("user", array("username", "password", "bot_op"), array($user, $psw, $op));
			} else {
				if($psw != "")
					$this->update("user", array("password"), array($psw), array("username"), array("="), array($user));
			}

			return $iduser;
		}

		/**
		  * Checks if user is registered
		  *
		  * @param $user
		  */
		function user_isregistered($user)
		{
			$iduser = $this->check_user($user);
			$result = $this->select(array("user"), array("password"), array(""), array("IDUser"), array("="), array($iduser));

			return($result[0]['password'] == "");
		}

		/**
		  * Checks if given password matches the one crypted in Bot's database
		  *
		  * @param $user     {STRING} Name of the user
		  * @param $password {STRING} Password to check
		  *
		  * @returns {BOOLEAN} True if passwords matches, else False
		  */
		function check_password($user, $password)
		{
			$iduser = $this->check_user($user);
			$result = $this->select(array("user"), array("password"), array(""), array("IDUser"), array("="), array($iduser));

			return(md5($password) == $result[0]['password']);
		}

		/**
		  * Similar to find_chan() but if user is not present, then add it to database
		  *
		  * @param $chan {STRING} Name of the channel
		  *
		  * @returns {INTEGER} ID number of channel
		  */
		function check_chan($chan)
		{
			$idchan = $this->find_chan($chan);

			if($idchan == 0)
				return $this->insert("chan", array("name"), array($chan));

			return $idchan;
		}

		/**
		  * Checks if given greeting message is present on Bot's database
		  *
		  * @param $message {STRING} Greeting message
		  *
		  * @returns {INTEGER} ID number of the message
		  */
		function check_greet($message)
		{
			$result = $this->select(array("greet"), array("IDGreet"), array(""), array("join_message"), array("="), array($message));

			if(count($result) == 0)
				return $this->insert("greet", array("join_message"), array($message));

			return (int)$result[0]['IDGreet'];
		}

		/**
		  * Permits to manage modes and greeting message
		  *
		  * @param $user  {STRING} User name
		  * @param $chan  {STRING} Channel
		  * @param $greet {STRING} Greeting message
		  * @param $modes {STRING} Modes to set
		  */
		function check_enter($user, $chan, $greet, $modes)
		{
			$iduser = $this->check_user($user);
			$idchan = $this->check_chan($chan);
			$idgreet = $this->check_greet($greet);

			$result = $this->select(array("enter"), array("*"), array(""), array("user_IDUser", "chan_IDChan", "greet_IDGreet"), array("=", "=", "="), array($iduser, $idchan, $idgreet));
			if(count($result) == 1)
				$this->update("enter", array("modes"), array($modes), array("user_IDUser", "chan_IDChan", "greet_IDGreet"), array("=", "=", "="), array($iduser, $idchan, $idgreet));
			else {
				$result = $this->select(array("enter"), array("*"), array(""), array("user_IDUser", "chan_IDChan"), array("=", "="), array($iduser, $idchan));
				if(count($result) == 1)
					$this->update("enter", array("modes", "IDGreet"), array($modes, $idsaluto), array("user_IDUser", "chan_IDChan"), array("=", "="), array($iduser, $idchan));
				else
					$this->insert("enter", array("user_IDUser", "chan_IDChan", "greet_IDGreet", "modes"), array($iduser, $idchan, $idsaluto, $modes));
			}
		}

		/**
		  * Finds greeting message for specified user and chan
		  *
		  * @param $user {STRING} User name
		  * @param $chan {STRING} Channel
		  *
		  * @returns {STRING} Message if set else an empty string
		  */
		function get_greet($user, $chan)
		{
			$iduser = $this->check_user($user);
			$idchan = $this->check_chan($chan);

			$result = $this->select(array("enter", "greet"), array("join_message"), array(""), array("greet_IDGreet", "chan_IDChan", "user_IDUser"), array("=", "=", "="), array("IDGreet", $idchan, $iduser));

			if(count($result) != 0)
				return $result[0]['join_message'];

			return "";
		}

		/**
		  * Finds modes to apply for specified user and chan
		  *
		  * @param $user {STRING} User name
		  * @param $chan {STRING} Channel
		  *
		  * @returns {STRING} Modes if set else an empty string
		  */
		function get_modes($user, $chan)
		{
			$iduser = $this->check_user($user);
			$idchan = $this->check_chan($chan);

			$result = $this->select(array("enter"), array("modes"), array(""), array("chan_IDChan", "user_IDUser"), array("=", "="), array($idchan, $iduser));

			if(count($result) != 0)
				return $result[0]['modes'];

			return "";
		}

		/**
		  * Gets the list of bot operators
		  *
		  * @returns {ARRAY} List of operators
		  */
		function get_operators()
		{
			$result = $this->select(array("user"), array("username"), array(""), array("bot_op"), array("="), array("true"));

			///TODO: Verificare qui
			$array = array();

			foreach($result as $r)
				$array[] = $r['username'];

			return $array;
		}

		/**
		  * Deletes greeting message for specified user and chan
		  *
		  * @param $user {STRING} User name
		  * @param $chan {STRING} Channel
		  */
		function del_greet($user, $chan)
		{
			$iduser = $this->check_user($user);
			$idchan = $this->check_chan($chan);

			$this->remove("enter", array("user_IDUser", "chan_IDChan"), array("=", "="), array($iduser, $idchan));
		}

		/**
		  * Removes bot operator title for specified user
		  *
		  * @param $user {STRING} User name
		  */
		function del_operator($user)
		{
			$iduser = $this->check_user($user);

			$this->update("user", array("bot_op"), array("false"), array("IDUser"), array("="), array($iduser));
		}

		/**
		  * Gives bot operator title for specified user
		  *
		  * @param $user {STRING} User name
		  */
		function set_operator($user)
		{
			global $operators;

			$iduser = $this->check_user($user);
			$this->update("user", array("bot_op"), array("true"), array("IDUser"), array("="), array($iduser));

			$operators = $this->get_operators();
			
		}
	}
?>