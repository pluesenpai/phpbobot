<?php

	$bad_words = "";
	$moderated = "";
	$max_kicks = 3;

	function moderating_init()
	{
		global $db;

		if(!$db->field_is_present("enter", "kicks")) {
			$field1 = array('fieldname' => "kicks", 'type' => "integer", 'size' => 0, 'null' => "", 'flags' => array("default:0"));
			$db->alter_table("enter", $field1);
			$db->update("enter", array("kicks"), array(0), array("kicks"), array("="), array("NULL"));
		}

		if(!$db->field_is_present("chan", "moderated")) {
			$field1 = array('fieldname' => "moderated", 'type' => "boolean", 'size' => 0, 'null' => "", 'flags' => array("default:FALSE"));
			$db->alter_table("chan", $field1);
			$db->update("chan", array("moderated"), array("FALSE"), array("moderated"), array("="), array("NULL"));
		}

		if(!$db->table_is_present("bad_words")) {
			//CREATE TABLE bad_words (IDWord INTEGER PRIMARY KEY NOT NULL, word VARCHAR(128) NOT NULL, count INTEGER DEFAULT 0 NOT NULL)
			$field1 = array('fieldname' => "IDWord", 'type' => "integer", 'size' => 0, 'null' => "not", 'flags' => array("primary", "ai"));
			$field2 = array('fieldname' => "word", 'type' => "varchar", 'size' => 128, 'null' => "not", 'flags' => array("unique"));
			$field3 = array('fieldname' => "count", 'type' => "integer", 'size' => 0, 'null' => "not", 'flags' => array());
			$db->create_table("bad_words", $field1, $field2, $field3);
		}

		if(!$db->table_is_present("forbidden")) { //args[] = array('PK' => array(PK_fields))
			//CREATE TABLE forbidden (IDword INTEGER NOT NULL, IDChan INTEGER NOT NULL, FOREIGN KEY (IDword) references bad_words(IDword), FOREIGN KEY (IDChan) references chan(IDChan))
			$moderated_forbidden_field1 = array('fieldname' => "IDBadWord", 'type' => "integer", 'size' => 0, 'null' => "not", 'flags' => array("references bad_words IDWord"));
			$moderated_forbidden_field2 = array('fieldname' => "IDChannel", 'type' => "integer", 'size' => 0, 'null' => "not", 'flags' => array("references chan IDChan"));
			$moderated_forbidden_pk = array("PK" => array("IDBadWord", "IDChannel"));
			$db->create_table("forbidden", $moderated_forbidden_field1, $moderated_forbidden_field2, $moderated_forbidden_pk);
		}

		moderating_update();
	}

	function moderating_update()
	{
		global $bad_words;
		global $moderated;

		$bad_words = refresh_badwords();
		$moderated = refresh_moderated();
	}

	function refresh_badwords() {
		global $db;
		global $irc_chans;

		foreach($irc_chans as $_chan) {
			$bad_words[$_chan] = array();
		}

		$result = $db->select(
			array("chan", "bad_words", "forbidden"),
			array("name", "word"),
			array("", ""),
			array("IDChannel", "IDBadWord"),
			array("=", "="),
			array("IDChan", "IDWord")
		);
		foreach($result as $r) {
			$chan = $r['name'];
			$w = $r['word'];
			$bad_words[$chan][] = $w;
		}

		return $bad_words;
	}

	function refresh_moderated() {
		global $db;
		global $irc_chans;

		$db->update("chan", array("moderated"), array("false"), array("moderated"), array("="), array("NULL"));
		$db->update("enter", array("kicks"), array(0), array("kicks"), array("="), array("NULL"));

		foreach($irc_chans as $_chan) {
			$moderated[$_chan] = array();
		}

		$result = $db->select(array("chan"), array("name", "moderated"), array("", ""), array(), array(), array());
		foreach($result as $r) {
			$chan = $r['name'];
			if($r['moderated'] == 'true')
				$moderated[$chan] = true;
			else
				$moderated[$chan] = false;
		}

		return $moderated;
	}

?>