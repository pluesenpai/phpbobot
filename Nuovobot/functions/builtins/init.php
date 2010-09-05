<?php
	$allowed_autolists = array(
		"a" => "o",
		"s" => "a",
		"v" => "v",
		"h" => "h",
		"q" => "q"
	);

	function builtins_init()
	{
		$db->update("user", array("auth"), array("false"), array(), array(), array());
		$db->update("chan", array("talk"), array("true"), array(), array(), array());
	}

	function builtins_update()
	{
		global $auth, $parla, $db;
		echo "called builtins_update\n";

		$result = $db->select(array("user"), array("username", "auth"), array("", ""), array(), array(), array());

		foreach($result as $r)
			if(array_key_exists($r["username"], $auth))
				$auth[$r["username"]] = getBoolFromDB($r["auth"]);

		$result2 = $db->select(array("chan"), array("name", "talk"), array("", ""), array(), array(), array());

		foreach($result2 as $r) {
			if(array_key_exists($r["name"], $parla))
				if(preg_match("/^#(.+)/", $r["name"]))
					$parla[$r["name"]] = getBoolFromDB($r["talk"]);
		}
	}
?>
