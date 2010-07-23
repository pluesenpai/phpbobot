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
				if($r["auth"] == "TRUE" || $r["auth"] == "true" || $r["auth"] == "1" || $r["auth"] == 1 || $r["auth"] == true)
					$auth[$r["username"]] = true;
				else
					$auth[$r["username"]] = false;

		$result = $db->select(array("chan"), array("name", "talk"), array("", ""), array(), array(), array());

		foreach($result as $r)
			if(array_key_exists($r["name"], $parla))
				if($r["talk"] == "TRUE" || $r["talk"] == "true" || $r["talk"] == "1" || $r["talk"] == 1 || $r["talk"] == true)
					$parla[$r["name"]] = true;
				else
					$parla[$r["name"]] = false;
	}
?>
