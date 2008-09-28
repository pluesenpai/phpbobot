<?php

$operators = get_from_file("functions/operators/operators.txt");

function operators_update()
{
  global $operators;
  $operators = get_from_file("functions/operators/operators.txt");
  echo "operators_update done\n";
}

?>