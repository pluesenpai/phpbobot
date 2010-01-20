<?php

function operators_update()
{
	global $operators, $db;

	$operators = $db->get_operators();
}

operators_update();

?>