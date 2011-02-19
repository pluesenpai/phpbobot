<?php

	function quotes_init()
	{
		global $db;

		if(!$db->table_is_present("quotes")) {
			$quotes_field0 = array("fieldname" => "IDQuote", "type" => "integer", "size" => 0, "null" => "not", "flags" => array("primary", "ai"));
			$quotes_field1 = array("fieldname" => "message", "type" => "blob", "size" => 0, "null" => "not", "flags" => array());
			$quotes_field2 = array("fieldname" => "sender", "type" => "integer", "size" => 0, "null" => "not", "flags" => array("references user IDUser"));
			$quotes_field3 = array("fieldname" => "poster", "type" => "integer", "size" => 0, "null" => "not", "flags" => array("references user IDUser"));
			$quotes_field4 = array("fieldname" => "channel", "type" => "integer", "size" => 0, "null" => "not", "flags" => array("references chan IDChan"));
// 			$quotes_pks = array("PK" => array("message", "sender", "channel"));
			$quotes_unique = array("UNIQUE" => array("message", "sender", "channel"));

			$db->create_table("quotes", $quotes_field0, $quotes_field1, $quotes_field2, $quotes_field3, $quotes_field4, $quotes_unique);
		}

		if(file_exists("quotes.txt"))
			unlink("quotes.txt");
	}

	function quotes_update()
	{
		return;
	}
?>