<?php

	function messages_init()
	{
		global $db;

		//Verifica della presenza delle tabelle necessarie nel DB
		if(!$db->table_is_present("message")) {
			$message_field1 = array("fieldname" => "IDMsg", "type" => "integer", "size" => 0, "null" => "not", "flags" => array("primary", "ai"));
			$message_field2 = array("fieldname" => "message", "type" => "blob", "size" => 0, "null" => "not", "flags" => array());
			$message_field3 = array("fieldname" => "data", "type" => "date", "size" => 0, "null" => "not", "flags" => array());
			$message_field4 = array("fieldname" => "letto", "type" => "boolean", "size" => 0, "null" => "not", "flags" => array());
			$message_field5 = array("fieldname" => "notified", "type" => "boolean", "size" => 0, "null" => "not", "flags" => array());
			$message_field6 = array("fieldname" => "IDFrom", "type" => "varchar", "size" => 80, "null" => "not", "flags" => array("references user IDUser"));
			$message_field7 = array("fieldname" => "IDTo", "type" => "varchar", "size" => 80, "null" => "not", "flags" => array("references user IDUser"));

			$db->create_table("message", $message_field1, $message_field2, $message_field3, $message_field4, $message_field5, $message_field6, $message_field7);
		}
	}

	function messages_update()
	{
		return;
	}
?>