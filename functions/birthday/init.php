<?php

	function birthday_init() {
		global $db;

		if(!$db->field_is_present("user", "birthday")) {
			$birthday_field1 = array('fieldname' => "birthday", 'type' => "date", 'size' => 0, 'null' => "", 'flags' => array());
			$db->alter_table("user", $birthday_field1);
		}
	}

	function birthday_update()
	{
		return;
	}

?>
