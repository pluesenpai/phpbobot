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

	}

	function builtins_update()
	{
		global $auth, $parla;

		$result = $this->select(array("user"), array("username", "auth"), array(""), array(), array(), array());

		print_r($result);

		/*foreach($result as $r)
			if(array_key_exists($r["username"], $auth))
				$auth[$r["username"]] = $r["auth"];*/
				
		$result = $this->select(array("chan"), array("name", "talk"), array(""), array(), array(), array());

		print_r($result);

		/*foreach($result as $r)
			if(array_key_exists($r["name"], $parla))
				$parla[$r["name"]] = $r["talk"];*/
	}
?>
