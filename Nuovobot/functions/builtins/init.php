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

		$result = $db->select(array("user"), array("username", "auth"), array("", ""), array(), array(), array());

		foreach($result as $r)
			if(array_key_exists($r["username"], $auth))
				$auth[$r["username"]] = getBoolFromDB($r["auth"]);

		$result2 = $db->select(array("chan"), array("name", "talk"), array("", ""), array(), array(), array());

		foreach($result2 as $r2) {
			if(array_key_exists($r2["name"], $parla))
				if(preg_match("/^#(.+)/", $r2["name"]))
					$parla[$r2["name"]] = getBoolFromDB($r2["talk"]);
		}
	}
?>
