<?php

	$is_bpp_on = "";

	function bpp_init() {
		global $db;

		$bpp_field3 = array('fieldname' => "description", 'type' => "varchar", 'size' => 150, 'null' => "not", 'flags' => array());
		if(!$db->table_is_present("bpp")) {
			$bpp_field1 = array('fieldname' => "var", 'type' => "varchar", 'size' => 80, 'null' => "not", 'flags' => array("primary"));
			$bpp_field2 = array('fieldname' => "meaning", 'type' => "varchar", 'size' => 80, 'null' => "not", 'flags' => array());
			$db->create_table("bpp", $bpp_field1, $bpp_field2, $bpp_field3);
		}

		if(!$db->field_is_present("bpp", "description")) {
			$db->alter_table("bpp", $bpp_field3);
			$db->update("bpp", array("description"), array(""), array("description"), array("="), array("NULL"));
		}

		if(!$db->field_is_present("chan", "is_bpp_on")) {
			$bpp_field1 = array('fieldname' => "is_bpp_on", 'type' => "boolean", 'size' => 0, 'null' => "", 'flags' => array());
			$db->alter_table("chan", $bpp_field1);
			$db->update("user", array("is_bpp_on"), array("false"), array("is_bpp_on"), array("="), array("NULL"));
		}

		bpp_update();
	}

	function bpp_update()
	{
		global $is_bpp_on;
	
		$is_bpp_on = getBPPValue();
	}

	function getBPPValue()
	{
		global $db;

		$is_bpp_on = array();

		$db->update("chan", array("is_bpp_on"), array("false"), array("is_bpp_on"), array("="), array("NULL"));
		$result = $db->select(array("chan"), array("name", "is_bpp_on"), array("", ""), array(), array(), array());

		foreach($result as $r) {
			$chan = $r["name"];
			$enabled = $r["is_bpp_on"];
			if(strtolower($enabled) == "true")
				$is_bpp_on[$chan] = true;
			else
				$is_bpp_on[$chan] = false;
		}

		return $is_bpp_on;
	}

?>
