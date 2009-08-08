<?php

if(!$db->field_is_present("user", "birthday")) {
	$field1 = array('fieldname' => "birthday", 'type' => "date", 'size' => 0, 'null' => "", 'flags' => array());
	$db->alter_table("user", $field1);
}

function birthday_update()
{
	return;
}

?>
