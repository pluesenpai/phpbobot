<?php

	if(!$db->table_is_present("bpp")) {
		$bpp_field1 = array('fieldname' => "var", 'type' => "varchar", 'size' => 80, 'null' => "not", 'flags' => array("primary"));
		$bpp_field2 = array('fieldname' => "meaning", 'type' => "varchar", 'size' => 80, 'null' => "not", 'flags' => array());
		$db->create_table("bpp", $bpp_field1, $bpp_field2);
		unset($bpp_field1);
		unset($bpp_field2);
	}

	if(!$db->field_is_present("chan", "is_bpp_on")) {
		$bpp_field1 = array('fieldname' => "is_bpp_on", 'type' => "boolean", 'size' => 0, 'null' => "", 'flags' => array());
		$db->alter_table("chan", $bpp_field1);
		$db->update("user", array("is_bpp_on"), array("false"), array("is_bpp_on"), array("="), array("NULL"));
		unset($bpp_field1);
	}

	$is_bpp_on = getBPPValue();

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

	function bpp_update()
	{
		global $is_bpp_on;
	
		$is_bpp_on = getBPPValue();
	}

?>
