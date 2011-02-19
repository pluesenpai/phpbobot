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
		global $db;

		$db->update("user", array("auth"), array("false"), array(), array(), array());
		$db->update("chan", array("talk"), array("true"), array(), array(), array());

		builtins_update();
	}

	function builtins_update()
	{
		global $auth, $parla, $db, $saluta, $salutanuovi, $irc_chans;

		$result = $db->select(array("user"), array("username", "auth"), array("", ""), array(), array(), array());

		foreach($result as $r)
			if(array_key_exists($r["username"], $auth))
				$auth[$r["username"]] = $db->getBoolFromDB($r["auth"]);

		$result2 = $db->select(array("chan"), array("name", "talk"), array("", ""), array(), array(), array());

		foreach($result2 as $r2) {
			if(array_key_exists($r2["name"], $parla))
				if(preg_match("/^#(.+)/", $r2["name"]))
					$parla[$r2["name"]] = $db->getBoolFromDB($r2["talk"]);
		}

		for($i = 0; $i < count($irc_chans); $i++) {
			$result = $db->select(array("chan"), array("greet", "greetnew"), array("", ""), array("name"), array("="), array($irc_chans[$i]));
			foreach($result as $r) {
				$saluta[$irc_chans[$i]] = $db->getBoolFromDB($r["greet"]);
				$salutanuovi[$irc_chans[$i]] = $db->getBoolFromDB($r["greetnew"]);
			}
		}
	}
?>
