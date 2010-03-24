<?php
	require("database.interface.php");

	/** Abstract class that dialogues with DBMS */
	abstract class DBHandler implements iDatabase
	{
		private $_dbname;
		private $_dbhandle;
		private $_dbuser;
		private $_dbpass;
		private $_dbhost;
		private $_dbport;

		/**
		  * Constructor of the class
		  *
		  * @param $dbname {STRING}  [DEFAULT="bobot"]      Name of database
		  * @param $dbhost {STRING}  [DEFAULT="localhost"]  Host to connect to
		  * @param $dbport {INTEGER} [DEFAULT="3306"]       Port to connect to
		  * @param $dbuser {STRING}  [DEFAULT="bobot"]      User of the database
		  * @param $dbpass {STRING}  [DEFAULT="bobot"]      Password of the database
		  */
		function __construct($dbname = "bobot", $dbhost = "localhost", $dbport = "3306", $dbuser = "bobot", $dbpass = "bobot")
		{
			$this->_dbname = $dbname;
			$this->_dbhost = $dbhost;
			$this->_dbport = $dbport;
			$this->_dbuser = $dbuser;
			$this->_dbpass = $dbpass;

			$this->_dbhandle = new PDO("mysql:host={$this->_dbhost};dbname={$this->_dbname}", $this->_dbuser, $this->_dbpass, array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8"));
		}

		/**
		  * Destructor of the class
		  */
		function __destruct()
		{
			$this->_dbhandle = NULL;
		}

		/**
		  * Tells which type of DBMS uses this class
		  *
		  * @returns {STRING} DBMS used by the class
		  */
		function dbtype()
		{
			return "pdo_mysql";
		}

		function getDBName()
		{
			return $this->_dbname;
		}

		function getHost()
		{
			return "";
		}

		function getPort()
		{
			return "";
		}

		function getUser()
		{
			return "";
		}

		/**
		  * Permits to create a new table in the bot
		  *
		  * You can put how many arguments you want
		  */
		function create_table()
		{
			$n_args = func_num_args();
			$args = func_get_args();
				//args[0] = tablename
				//args[$i > 0] = array('fieldname' => "", 'type' => "", 'size' => "", 'null' => "", 'flags' => "")
				// ... ... ...
				//args[] = array('PK' => array(PK_fields))
				//args[] = array('FK' => array(FK_fields))
				//args[] = array('CHECK' => array(check_fields))
				//args[] = array('UNIQUE' => array(unique_fields))

			$query = "CREATE TABLE {$args[0]} (";
			for($i = 1; $i < $n_args && array_key_exists("fieldname", $args[$i]); $i++) {
				$query .= $args[$i]['fieldname'];

				switch($args[$i]['type']) {
					case 'integer':
					case 'varchar':
					case 'char':
					case 'date':
					case 'time':
					case 'blob':
					case 'boolean':
						$type = strtoupper($args[$i]['type']);
						break;
				}

				if($args[$i]['size'] > 0)
					$type .= "(" . $args[$i]['size'] . ")";

				$query .= " $type";
				foreach($args[$i]['flags'] as $flag) {
					//references TABLE FIELD ON_UPDATE ON_DELETE
					if(preg_match("/^references (.+?) (.+?) (.+?) (.+?)$/", $flag, $data))
						$query .= " REFERENCES $data[1]($data[2]) ON UPDATE $data[3] ON DELETE $data[4]";
					elseif($flag == "primary")
						$query .= " PRIMARY KEY";
					elseif($flag == "AI")
						$query .= "";
					elseif(preg_match("/^check (.+)$/", $flag, $data))
						$query .= " CHECK (" . $args[$i]['fieldname'] . " $data[1])";
					elseif($flag == "unique")
						$query .= " UNIQUE";
					elseif(preg_match("/^default:(.+)$/", $flag, $data)) {
						$sep = "";
						if(!is_numeric($data[1]))
							$sep = "\"";
						$query .= " DEFAULT {$sep}{$data[1]}{$sep}";
					}
				}
				if($args[$i]['null'] == 'not')
					$query .= " NOT NULL, ";
			}

			for( ; $i < $n_args; $i++) {
				if(array_key_exists("PK", $args[$i]))
					$query .= "PRIMARY KEY (" . implode(", ", $args[$i]["PK"]) . "), ";
				elseif(array_key_exists("UNIQUE", $args[$i]))
					$query .= "UNIQUE (" . implode(", ", $args[$i]["UNIQUE"]) . "), ";
				elseif(array_key_exists("FK", $args[$i])) {
					foreach($args[$i]["FK"] as $fk => $val) {
						preg_match("/^(.+?) (.+?) (.+?) (.+?)$/", $val, $data);
						$query .= "FOREIGN KEY ($fk) REFERENCES $data[1]($data[2]) ON UPDATE $data[3] ON DELETE $data[4], ";
					}
				} elseif(array_key_exists("CHECK", $args[$i])) {
					foreach($args[$i]["CHECK"] as $check) {
						$query .= "CHECK ($check), ";
					}
				}
			}

			if(substr($query, -2) == ", ")
				$query = substr($query, 0, -2);

			$query .= ")";

			$this->_dbhandle->exec($query);
		}

		function alter_table()
		{
			$n_args = func_num_args();
			$args = func_get_args();
				//args[0] = tablename
				//args[$i > 0] = array('fieldname' => "", 'type' => "", 'size' => "", 'null' => "", 'flags' => "")

			$q = "ALTER TABLE $args[0] ADD COLUMN ";
			for($i = 1; $i < $n_args; $i++) {
				$q .= $args[$i]['fieldname'];
				switch($args[$i]['type']) {
					case 'integer':
					case 'varchar':
					case 'char':
					case 'date':
					case 'time':
					case 'blob':
					case 'boolean':
						$type = strtoupper($args[$i]['type']);
						break;
				}
				if($args[$i]['size'] > 0)
					$type .= "(" . $args[$i]['size'] . ")";
				$q .= " $type";
				foreach($args[$i]['flags'] as $flag) {
					if(preg_match("/^references (.+?) (.+?) (.+?) (.+?)$/", $flag, $data))
						$q .= " REFERENCES $data[1]($data[2]) ON UPDATE $data[3] ON DELETE $data[4]";
					elseif($flag == "primary")
						$q .= " PRIMARY KEY";
					elseif($flag == "AI")
						$q .= "";
					elseif(preg_match("/^check (.+)$/", $flag, $data))
						$q .= " CHECK (" . $args[$i]['fieldname'] . " $data[1])";
					elseif($flag == "unique")
						$q .= " UNIQUE";
					elseif(preg_match("/^default:(.+)$/", $flag, $data)) {
						$sep = "";
						if(!is_numeric($data[1]))
							$sep = "\"";
						$query .= " DEFAULT {$sep}{$data[1]}{$sep}";
					}
				}
				if($args[$i]['null'] == 'not')
					$q .= " NOT NULL, ";
			}

			if(substr($q, -2) == ", ")
				$q = substr($q, 0, -2);

			$this->_dbhandle->exec($q);
		}


		function select($tables, $field, $as, $cond_f, $cond_o, $cond_v, $limit = 0, $sort = "", $group = "")
		{
			$q = "SELECT ";

			for($i = 0; $i < count($field); $i++) {
				$q .= $field[$i];
				if($as[$i] != "")
					$q .= " AS $as[$i]";
				$q .= ", ";
			}

			if(substr($q, -2) == ", ")
				$q = substr($q, 0, -2);

			$q .= " FROM " . implode(", ", $tables);

			if(count($cond_f) > 0) {
				$q .= " WHERE ";
				for($i = 0; $i < count($cond_f); $i++) {
					$add = "";
					for($j = 0, $condition = false; $j < count($tables) && $condition == false; $j++)
						$condition = $this->field_is_present($tables[$j], $cond_v[$i]);
					if(!is_numeric($cond_v[$i]) && $condition == false)
						$add = "\"";
					$q .= $cond_f[$i] . " " . $cond_o[$i] . " " . $add . $this->clean_text($cond_v[$i]) . $add . " AND ";
				}
				if(substr($q, -5) == " AND ")
					$q = substr($q, 0, -5);
			}

			if(preg_match("/^group (.+):(.+)$/", $group, $data)) {
				$q .= " GROUP BY $data[1] HAVING $data[2]";
			} elseif(preg_match("/^group (.+)$/", $group, $data)) {
				$q .= " GROUP BY $data[1]";
			}

			if(preg_match("/^asc\*(.+?)$|^desc\*(.+?)$/i", $sort, $field)) {
				$exploded = explode("*", $sort);
				$q .= " ORDER BY " . implode(array_slice($field, 1)) . " " . strtoupper($exploded[0]);
			}
			if(preg_match("/^random$/i", $sort)) {
				$q .= " ORDER BY RANDOM()";
			}

			if($limit > 0)
				$q .= " LIMIT $limit";

			echo "$q\n";

			$query = $this->_dbhandle->prepare($q);

			$query->execute();
			$result = $query->fetchAll();

			return $result;
		}

		function update($table, $fields, $values, $cond_f, $cond_o, $cond_v, $limit = 0)
		{
			$q = "UPDATE $table SET";

			for($i = 0; $i < count($fields); $i++) {
				$add = "";
				if(!is_numeric($values[$i]))
					$add = "\"";
				$q .= " $fields[$i]=" . $add . $values[$i] . $add . ", ";
			}

			if(substr($q, -2) == ", ")
				$q = substr($q, 0, -2);

			if(count($cond_f) > 0) {
				$q .= " WHERE ";
				for($i = 0; $i < count($cond_f); $i++) {
					$add = "";
					$condition = $this->field_is_present($table, $cond_v[$i]);
					if(!is_numeric($cond_v[$i]) && $condition == false)
						$add = "\"";
					$q .= $cond_f[$i] . " " . $cond_o[$i] . " " . $add . $this->clean_text($cond_v[$i]) . $add . " AND ";
				}
				if(substr($q, -5) == " AND ")
					$q = substr($q, 0, -5);
			}

			if($limit > 0)
				$q .= " LIMIT $limit";

			$this->_dbhandle->exec($q);
		}

		function remove($table, $cond_f, $cond_o, $cond_v, $limit = 0)
		{
			$q = "DELETE FROM $table WHERE ";
			for($i = 0; $i < count($cond_f); $i++) {
				$add = "";
				$condition = $this->field_is_present($table, $cond_v[$i]);
				if(!is_numeric($cond_v[$i]) && $condition == false)
					$add = "\"";
				$q .= $cond_f[$i] . " " . $cond_o[$i] . " " . $add . $this->clean_text($cond_v[$i]) . $add . " AND ";
			}
			if(substr($q, -5) == " AND ")
				$q = substr($q, 0, -5);

// 			if($limit > 0)
// 				$q .= " LIMIT $limit";

			$this->_dbhandle->exec($q);
		}

		function insert($table, $fields, $values)
		{
			$q = "INSERT INTO $table (" . implode(", ", $fields) . ") VALUES (";// . implode(", ", $values) . ")";
			for($i = 0; $i < count($values); $i++) {
				$add = "";
				if(!is_numeric($values[$i]))
					$add = "\"";
				$q .= $add . $this->clean_text($values[$i]) . $add . ", ";
			}

			if(substr($q, -2) == ", ")
				$q = substr($q, 0, -2);

			$q .= ")";

			$this->_dbhandle->exec($q);

			return $this->_dbhandle->lastInsertId();
		}


		function table_is_present($table)
		{
			$query = $this->_dbhandle->prepare("SHOW TABLES LIKE \"$table\"");
			$query->execute();
			$result = $query->fetchAll();

			if(count($result) == 0)
				return false;

			return true;
		}

		function field_is_present($table, $field)
		{
			if(preg_match("/^!(.+)!$/", $field))
				return true;

			$table_ = explode(" ", $table);
			$query = $this->_dbhandle->prepare("SHOW COLUMNS FROM $table_[0]");
			$query->execute();
			$result = $query->fetchAll();

			$trovato = false;
			foreach($result as $r) {
				if($r['Field'] == $field)
					$trovato = true;
			}

			return $trovato;
		}

		private function clean_text($text)
		{
			if(is_numeric($text))
				return $text;

			$cleaned = htmlentities($text, ENT_QUOTES, "UTF-8");
			$cleaned = preg_replace("/^!(.+)!$/", "\$1", $cleaned);

			return $cleaned;
		}
	}

?>