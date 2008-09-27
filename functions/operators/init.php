<?php

$operators = get_from_file("operators.txt");

function operators_update()
{
  global $operators;
  $operators = get_from_file("operators.txt");
  echo "operators_update done\n";
}

?>