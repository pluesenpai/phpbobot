<?php
class sqlite_db {

	private $_dbname;
	private $_dbhandle;

	function __construct($dbname = "database.db")
	{
		$this->_dbname = $dbname;
		if(!$this->create_db())
			//$this->_dbhandle = new SQLiteDatabase($this->_dbname);
			$this->_dbhandle = new PDO("sqlite:" . $this->_dbname);
	}

	function __destruct()
	{
		$this->_dbhandle = NULL;
	}

	function dbtype()
	{
		return "sqlite";
	}

	function create_db()
	{
		if(!file_exists($this->_dbname)) {
			//$this->_dbhandle = new SQLiteDatabase($this->_dbname);
			$this->_dbhandle = new PDO("sqlite:" . $this->_dbname);
			$this->_dbhandle->exec("CREATE TABLE saluto (IDSaluto INTEGER PRIMARY KEY NOT NULL, join_message VARCHAR(255) NOT NULL)");
			$this->_dbhandle->exec("CREATE TABLE chan (IDChan INTEGER PRIMARY KEY NOT NULL, name VARCHAR(255) NOT NULL)");
			$this->_dbhandle->exec("CREATE TABLE user (IDUser INTEGER PRIMARY KEY NOT NULL, username VARCHAR(80) UNIQUE NOT NULL, password CHAR(33), bot_op BOOLEAN DEFAULT FALSE NOT NULL)");
			$this->_dbhandle->exec("CREATE TABLE entra (IDUser INTEGER NOT NULL, IDChan INTEGER NOT NULL, IDSaluto INTEGER NOT NULL, modes VARCHAR(15), FOREIGN KEY (IDUser) references user(IDUser), FOREIGN KEY (IDChan) references chan(IDChan), FOREIGN KEY (IDSaluto) references saluto(IDSaluto))");
			return true;
		}

		return false;
	}

	function create_table()
	{
		$n_args = func_num_args();
		$args = func_get_args(); //args[0] = tablename, args[$i > 0] = array('fieldname' => "", 'type' => "", 'size' => "", 'null' => "", 'flags' => "")

		$query = "CREATE TABLE $args[0] (";
		for($i = 1; $i < $n_args; $i++) {
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
				if($flag == "primary")
					$query .= " PRIMARY KEY";
				elseif(preg_match("/^references (.+?) (.+?)$/", $flag, $data))
					$query .= " references $data[1]($data[2])";
				elseif($flag == "AI")
					$query .= "";
			}
			if($args[$i]['null'] == 'not')
				$query .= " NOT NULL, ";
		}

		if(substr($query, -2) == ", ")
			$query = substr($query, 0, -2);

		$query .= ")";

		$this->_dbhandle->exec($query);
	}

	function alter_table()
	{
		$n_args = func_num_args();
		$args = func_get_args(); //args[0] = tablename, args[$i > 0] = array('fieldname' => "", 'type' => "", 'size' => "", 'null' => "", 'flags' => "")

		$query = "ALTER TABLE $args[0] ADD ";
		for($i = 1; $i < $n_args; $i++) {
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
				if($flag == "primary")
					$query .= " PRIMARY KEY";
				elseif(preg_match("/^references (.+?) (.+?)$/", $flag, $data))
					$query .= " references $data[1]($data[2])";
				elseif($flag == "AI")
					$query .= "";
			}
			if($args[$i]['null'] == 'not')
				$query .= " NOT NULL, ";
		}

		if(substr($query, -2) == ", ")
			$query = substr($query, 0, -2);

		$this->_dbhandle->exec($query);
	}

	function select($tables, $field, $as, $cond_f, $cond_o, $cond_v)
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
			for($i = 0; $i < count($cond_f); $i++)
				$q .= $cond_f[$i] . $cond_o[$i] . $cond_v[$i] . " AND ";
			if(substr($q, -5) == " AND ")
				$q = substr($q, 0, -5);
		}

		$query = $this->_dbhandle->prepare($q);
		$query->execute();
		$result = $query->fetchAll();

		return $result;
	}

	function update($table, $fields, $values, $cond_f, $cond_o, $cond_v)
	{
		$q = "UPDATE $table SET";

		for($i = 0; $i < count($fields); $i++){
			$q .= " $fields[$i]=$values[$i], ";
		}

		if(substr($q, -2) == ", ")
			$q = substr($q, 0, -2);
		$q .= " WHERE ";
		for($i = 0; $i < count($cond_f); $i++)
			$q .= $cond_f[$i] . $cond_o[$i] . $cond_v[$i] . " AND ";
		if(substr($q, -5) == " AND ")
			$q = substr($q, 0, -5);

		$this->_dbhandle->exec($q);
	}

	function remove($table, $cond_f, $cond_o, $cond_v)
	{
		$q = "DELETE FROM $table WHERE ";
		for($i = 0; $i < count($cond_f); $i++)
			$q .= $cond_f[$i] . $cond_o[$i] . $cond_v[$i] . " AND ";
		if(substr($q, -5) == " AND ")
			$q = substr($q, 0, -5);

		echo "$q\n";
		$this->_dbhandle->exec($q);
	}

	function table_is_present($table)
	{
		$query = $this->_dbhandle->prepare("PRAGMA table_info($table)");
		$query->execute();
		$result = $query->fetchAll();

		if(count($result) == 0)
			return false;

		return true;
	}

	function field_is_present($table, $field)
	{
		$query = $this->_dbhandle->prepare("PRAGMA table_info($table)");
		$query->execute();
		$result = $query->fetchAll();

		$trovato = false;
		foreach($result as $r) {
			if($r['name'] == $field)
				$trovato = true;
		}

		return $trovato;
	}


	function insert($table, $fields, $values)
	{
		echo "INSERT INTO $table (" . implode(", ", $fields) . ") VALUES (" . implode(", ", $values) . ")\n";
		$this->_dbhandle->exec("INSERT INTO $table (" . implode(", ", $fields) . ") VALUES (" . implode(", ", $values) . ")");

		return $this->_dbhandle->lastInsertId();
	}

	function verifica_user($user, $password = "")
	{
		$query = $this->_dbhandle->prepare("SELECT IDUser FROM user WHERE username = '$user'");
		$query->execute();
		$result = $query->fetchAll();
		if($password != "")
			$psw = md5($password);
		else
			$psw = "";

		if(count($result) == 0) {
			if(in_array($user, array('paolo86', 'sardylan')))
				$op = 'true';
			else
				$op = 'false';
			$this->_dbhandle->exec("INSERT INTO user (username, password, bot_op) VALUES ('$user', '$psw', '$op')");

			return $this->_dbhandle->lastInsertId();
		} else {
			if($psw != "") {
				$query = $this->_dbhandle->prepare("UPDATE user SET password = '$psw' WHERE username = '$user'");
				$query->execute();
			}
		}

		return (int)$result[0]['IDUser'];
	}

	function verifica_password($user, $password)
	{
		$iduser = $this->verifica_user($user);
		$query = $this->_dbhandle->prepare("SELECT password FROM user WHERE IDUser = '$iduser'");
		$query->execute();
		$result = $query->fetchAll();

		return(md5($password) == $result[0]['password']);
	}

	function get_password($iduser)
	{
		$query = $this->_dbhandle->prepare("SELECT password FROM user WHERE IDUser = '$iduser'");
		$query->execute();
		$result = $query->fetchAll();

		return $result[0]['password'];
	}

	function verifica_chan($chan)
	{
		$query = $this->_dbhandle->prepare("SELECT IDChan FROM chan WHERE name = '$chan'");
		$query->execute();
		$result = $query->fetchAll();

		if(count($result) == 0) {
			$this->_dbhandle->exec("INSERT INTO chan (name) VALUES ('$chan')");

			return $this->_dbhandle->lastInsertID();
		}

		return (int)$result[0]['IDChan'];
	}

	function verifica_saluto($message)
	{
		$query = $this->_dbhandle->prepare("SELECT IDSaluto FROM saluto WHERE join_message = '$message'");
		$query->execute();
		$result = $query->fetchAll();

		if(count($result) == 0) {
			$this->_dbhandle->exec("INSERT INTO saluto (join_message) VALUES ('$message')");

			return $this->_dbhandle->lastInsertID();
		}

		return (int)$result[0]['IDSaluto'];
	}

	function get_saluto($iduser, $idchan)
	{
		$query = $this->_dbhandle->prepare("SELECT join_message FROM entra, saluto WHERE entra.IDSaluto = saluto.IDSaluto AND entra.IDChan = $idchan AND entra.IDUser = $iduser");
		$query->execute();
		$result = $query->fetchAll();

		if(count($result) != 0)
			return $result[0]['join_message'];
		else
			return "";
	}

	function del_saluto($iduser, $idchan)
	{
		$this->_dbhandle->exec("DELETE FROM entra WHERE IDUser = $iduser AND IDChan = $idchan");
	}

	function verifica_entra($iduser, $idchan, $idsaluto, $modes)
	{
		$query = $this->_dbhandle->prepare("SELECT * FROM entra WHERE IDUser = $iduser AND IDChan = $idchan AND IDSaluto = $idsaluto");
		$query->execute();
		$result = $query->fetchAll();
		if(count($result) == 1) {
			$this->_dbhandle->exec("UPDATE entra SET modes = '$modes' WHERE IDUser = $iduser AND IDChan = $idchan AND IDSaluto = $idsaluto");
		} else {
			$query = $this->_dbhandle->prepare("SELECT * FROM entra WHERE IDUser = $iduser AND IDChan = $idchan");
			$query->execute();
			$result = $query->fetchAll();
			if(count($result) == 1) {
				$this->_dbhandle->exec("UPDATE entra SET modes = '$modes', IDSaluto = $idsaluto WHERE IDUser = $iduser AND IDChan = $idchan");
			} else {
				$this->_dbhandle->exec("INSERT INTO entra VALUES ($iduser, $idchan, $idsaluto, 0, '$modes')");
			}
		}
	}

	function get_mode($iduser, $idchan)
	{
		$query = $this->_dbhandle->prepare("SELECT modes FROM entra WHERE IDChan = $idchan AND IDUser = $iduser");
		$query->execute();
		$result = $query->fetchAll();

		if(count($result) != 0)
			return $result[0]['modes'];
		else
			return "";
	}

	function get_operatori()
	{
		$query = $this->_dbhandle->prepare("SELECT username FROM user WHERE bot_op = 'true'");
		$query->execute();
		$result = $query->fetchAll();

		$array = array();

		foreach($result as $r)
			$array[] = $r['username'];

		return $array;
	}

	function set_operatore($iduser)
	{
		$this->_dbhandle->exec("UPDATE user SET bot_op = 'true' WHERE IDUser = $iduser");
	}

	function del_operatore($iduser)
	{
		$this->_dbhandle->exec("UPDATE user SET bot_op = 'false' WHERE IDUser = $iduser");
	}
}

$db = new sqlite_db();
?>