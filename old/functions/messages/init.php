<?php
// 	$dbname = "functions/messages/messages.db";

	//Verifica della presenza delle tabelle necessarie nel DB
	if(!$db->table_is_present("message")) {
		$field1 = array('fieldname' => "IDMsg", 'type' => "integer", 'size' => 0, 'null' => "not", 'flags' => array("primary", "ai"));
		$field2 = array('fieldname' => "message", 'type' => "blob", 'size' => 0, 'null' => "not", 'flags' => array());
		$field3 = array('fieldname' => "data", 'type' => "date", 'size' => 0, 'null' => "not", 'flags' => array());
		$field4 = array('fieldname' => "letto", 'type' => "boolean", 'size' => 0, 'null' => "not", 'flags' => array());
		$field5 = array('fieldname' => "notified", 'type' => "boolean", 'size' => 0, 'null' => "not", 'flags' => array());
		$field6 = array('fieldname' => "IDFrom", 'type' => "varchar", 'size' => 80, 'null' => "not", 'flags' => array("references user IDUser"));
		$field7 = array('fieldname' => "IDTo", 'type' => "varchar", 'size' => 80, 'null' => "not", 'flags' => array("references user IDUser"));

		$db->create_table("message", $field1, $field2, $field3, $field4, $field5, $field6, $field7);
	}

	function messages_update()
	{
		return;
	}
?>