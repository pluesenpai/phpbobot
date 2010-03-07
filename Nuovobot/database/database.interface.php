<?php

	/** Interface of class for database operations of the bot */
	interface iDatabase
	{
		function __construct($dbname, $dbhost, $dbport, $dbuser, $dbpass);
		function __destruct();
		
		function dbtype();
		
		function getDBName();
		function getHost();
		function getPort();
		function getUser();

		function create_table();
		function alter_table();

		function select($tables, $field, $as, $cond_f, $cond_o, $cond_v, $sort = "asc", $limit = 0);
		function update($table, $fields, $values, $cond_f, $cond_o, $cond_v, $limit = 0);
		function remove($table, $cond_f, $cond_o, $cond_v, $limit = 0);
		function insert($table, $fields, $values);

		function table_is_present($table);
		function field_is_present($table, $field);
	}
?>