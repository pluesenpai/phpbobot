<?php

	function corrections_init()
	{
		global $db;

		if(!$db->table_is_present("corrections")) {
			$corrections_field1 = array('fieldname' => "last_said", 'type' => "blob", 'size' => 0, 'null' => "not", 'flags' => array());
			$corrections_field2 = array('fieldname' => "user_IDUser", 'type' => "integer", 'size' => 0, 'null' => "not", 'flags' => array("references user IDUser"));
			$corrections_field3 = array('fieldname' => "chan_IDChan", 'type' => "integer", 'size' => 0, 'null' => "not", 'flags' => array("references chan IDChan"));
			$corrections_pks = array('PK' => array("user_IDUser", "chan_IDChan"));
			$db->create_table("corrections", $corrections_field1, $corrections_field2, $corrections_field3, $corrections_pks);
		}

		$db->update("corrections", array("last_said"), array(""), array(), array(), array());
	}

	function corrections_update()
	{
		return;
	}

?>