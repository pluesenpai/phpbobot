<?php

function refresh_paste()
{
	global $db;

	$paste_enabled = array();

	$db->update("user", array("paste_enabled"), array("false"), array("paste_enabled"), array("="), array("NULL"));
	$result = $db->select(array("user"), array("username", "paste_enabled"), array("", ""), array(), array(), array());

	foreach($result as $r) {
		$user = $r['username'];
		$enabled = $r['paste_enabled'];
		if(strtolower($enabled) == 'true')
			$paste_enabled[$user] = true;
		else
			$paste_enabled[$user] = false;
	}

	return $paste_enabled;
}

if(!$db->field_is_present("user", "paste_enabled")) {
	$field1 = array('fieldname' => "paste_enabled", 'type' => "boolean", 'size' => 0, 'null' => "", 'flags' => array());
	$db->alter_table("user", $field1);
	$db->update("user", array("paste_enabled"), array("false"), array("paste_enabled"), array("="), array("NULL"));
}

$paste_enabled = refresh_paste();
$dir_paste = "/tmp/{$user_name}/";
$paste_langs = array(
	"C (99)",
	"C++",
	"C#",
	"Java",
	"Pascal",
	"Perl",
	"PHP",
	"PL/I",
	"Ruby",
	"SQL",
	"Visual Basic",
	"Plain Text"
);

$paste_langs1 = array(
	"C",
	"C++",
	"C#",
	"Java",
	"Pascal",
	"Perl",
	"PHP",
	"PL/I",
	"Ruby",
	"SQL",
	"VB",
	"Text"
);


function paste_update()
{
	global $paste_enabled;

	$paste_enabled = refresh_paste();
}

?>