<?php

$operators = list_operatori($db);
//$operators = get_from_file("functions/operators/operators.txt");

function operators_update()
{
	global $operators;
	global $db;

	$operators = list_operatori($db);
	//$operators = get_from_file("functions/operators/operators.txt");
}

?>