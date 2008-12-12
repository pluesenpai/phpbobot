<?php

$bad_words = get_from_file("functions/moderating/bad_words.txt");
$moderated = get_from_file("functions/moderating/moderated.txt");

function moderating_update()
{
	global $bad_words;
	global $moderated;
	$bad_words = get_from_file("functions/moderating/bad_words.txt");
	$moderated = get_from_file("functions/moderating/moderated.txt");
	if($moderated[0] == 0)
		$moderated = false;
	else
		$moderated = true;
}

?>