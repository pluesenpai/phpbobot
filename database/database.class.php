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
			$this->create_db();
		}

		/**
		  * Destructor of the class
		  */
		function __destruct()
		{
			parent::__destruct();
		}

		/**
		  * Creates the initial tables needed by the bot
		  *
		  * @returns {BOOLEAN} false if database already exists, else true
		  */
		function create_db()
		{
			$tables = array(
				"greet" => array(
					"fields",
					array("fieldname" => "IDGreet", "type" => "integer", "size" => 0, "null" => "not", "flags" => array("primary", "ai")),
					array("fieldname" => "join_message", "type" => "varchar", "size" => 255, "null" => "not", "flags" => array("unique"))
				),
				"chan" => array(
					"fields",
					array("fieldname" => "IDChan", "type" => "integer", "size" => 0, "null" => "not", "flags" => array("primary", "ai")),
					array("fieldname" => "name", "type" => "varchar", "size" => 255, "null" => "not", "flags" => array("unique")),
					array("fieldname" => "talk", "type" => "boolean", "size" => 0, "null" => "not", "flags" => array("default:TRUE")),
					array("fieldname" => "greet", "type" => "boolean", "size" => 0, "null" => "not", "flags" => array("default:FALSE")),
					array("fieldname" => "greetnew", "type" => "boolean", "size" => 0, "null" => "not", "flags" => array("default:FALSE"))
				),
				"user" => array(
					"fields",
					array("fieldname" => "IDUser", "type" => "integer", "size" => 0, "null" => "not", "flags" => array("primary", "ai")),
					array("fieldname" => "username", "type" => "varchar", "size" => 80, "null" => "not", "flags" => array("unique")),
					array("fieldname" => "password", "type" => "char", "size" => 33, "null" => "yes", "flags" => array()),
					array("fieldname" => "bot_op", "type" => "boolean", "size" => 0, "null" => "not", "flags" => array("default:FALSE")),
					array("fieldname" => "alias", "type" => "integer", "size" => 0, "null" => "yes", "flags" => array("references user IDUser CASCADE CASCADE")),
					array("fieldname" => "auth", "type" => "boolean", "size" => 0, "null" => "not", "flags" => array("default:FALSE"))
				),
				"enter" => array(
					"fields",
					array("fieldname" => "user_IDUser", "type" => "integer", "size" => 0, "null" => "not", "flags" => array("references user IDUser CASCADE CASCADE")),
					array("fieldname" => "chan_IDChan", "type" => "integer", "size" => 0, "null" => "not", "flags" => array("references chan IDChan CASCADE CASCADE")),
					array("fieldname" => "greet_IDGreet", "type" => "integer", "size" => 0, "null" => "not", "flags" => array("references greet IDGreet CASCADE CASCADE")),
					array("fieldname" => "modes", "type" => "varchar", "size" => 15, "null" => "yes", "flags" => array()),
					array("fieldname" => "cangreet", "type" => "boolean", "size" => 0, "null" => "not", "flags" => array("default:FALSE")),
					array('PK' => array("user_IDUser", "chan_IDChan"))
				),
				"poke" => array(
					"fields",
					array("fieldname" => "IDPoke", "type" => "integer", "size" => 0, "null" => "not", "flags" => array("primary", "ai")),
					array("fieldname" => "poke_message", "type" => "varchar", "size" => 255, "null" => "not", "flags" => array("unique")),
					array("fieldname" => "user_IDUser", "type" => "integer", "size" => 0, "null" => "not", "flags" => array("references user IDUser CASCADE CASCADE"))
				)
			);

			foreach($tables as $table => $fields) {
				if(!$this->table_is_present($table)) {
					$this->create_table($table, $fields);
				} else {
					$fields2 = array_slice($fields, 1);
					foreach($fields2 as $field) {
						if(array_key_exists("fieldname", $field) && !$this->field_is_present($table, $field["fieldname"])) {
							$this->alter_table($table, $field);
						}
					}
				}
			}
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
				if(in_array($user, array("paolo86", "plue", "sardylan")))
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

			return($result[0]["password"] != "");
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
		  * @param $iduser  {STRING} User name
		  * @param $idchan  {STRING} Channel
		  * @param $idgreet {STRING} Greeting message
		  * @param $modes   {STRING} Modes to set
		  */
		function check_enter($iduser, $idchan, $idgreet, $modes)
		{
			$result = $this->select(array("enter"), array("*"), array(""), array("user_IDUser", "chan_IDChan", "greet_IDGreet"), array("=", "=", "="), array($iduser, $idchan, $idgreet));
			if(count($result) == 1)
				$this->update("enter", array("modes"), array($modes), array("user_IDUser", "chan_IDChan", "greet_IDGreet"), array("=", "=", "="), array($iduser, $idchan, $idgreet));
			else {
				$result = $this->select(array("enter"), array("*"), array(""), array("user_IDUser", "chan_IDChan"), array("=", "="), array($iduser, $idchan));
				if(count($result) == 1)
					$this->update("enter", array("modes", "IDGreet"), array($modes, $idgreet), array("user_IDUser", "chan_IDChan"), array("=", "="), array($iduser, $idchan));
				else
					$this->insert("enter", array("user_IDUser", "chan_IDChan", "greet_IDGreet", "modes"), array($iduser, $idchan, $idgreet, $modes));
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

		/**
		  * Checks if user is in database
		  *
		  * @param $user {STRING} User name
		  */
		function isnewuser($user)
		{
			return $this->find_user($user) == 0 ? true : false;
		}

		/**
		  * Checks if user wants to be greeted when joins channel
		  *
		  * @param $user {STRING} User name
		  */
		function cangreet($user, $channel)
		{
			$result = $this->select(array("enter"), array("cangreet"), array(""), array("user_IDUser", "chan_IDChan"), array("=", "="), array($this->find_user($user), $this->find_chan($channel)));

			foreach($result as $r)
				return $this->getBoolFromDB($r["cangreet"]);
		}

		function getBoolFromDB($field)
		{
			if($field === "TRUE" || $field === "true" || $field === "1" || $field === 1 || $field === true)
				return true;
			else
				return false;
		}
	}
?>