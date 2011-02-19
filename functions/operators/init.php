<?php

	function operators_init()
	{
		operators_update();
	}

	function operators_update()
	{
		global $operators, $db;

		$operators = $db->get_operators();
	}

?>