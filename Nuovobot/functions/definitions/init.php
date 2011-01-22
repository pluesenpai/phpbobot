<?php

	function definitions_init()
	{
		global $db;

		if(!$db->table_is_present("definitions")) {
			$definitions_field0 = array("fieldname" => "def_id", "type" => "integer", "size" => 0, "null" => "not", "flags" => array("primary", "ai"));
			$definitions_field1 = array("fieldname" => "def_name", "type" => "varchar", "size" => 30, "null" => "not", "flags" => array());
			$definitions_field2 = array("fieldname" => "def_text", "type" => "text", "size" => 0, "null" => "not", "flags" => array());

			$db->create_table("definitions", $definitions_field0, $definitions_field1, $definitions_field2);
		}
	}

	function definitions_update()
	{
		return;
	}
?>