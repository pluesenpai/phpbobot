<?php

function roulette($socket, $channel, $sender, $msg, $infos)
{
	global $users;

	$words = explode(",", $infos[1]);
	$word = trim($words[rand(0, count($words) - 1)]);

	echo $channel . " " . $sender;

	if($channel != $sender && is_user_in_chan($word, $channel))
		sendmsg($socket, "$word, sei stato nominato... ca\$\$i tuoi!", $channel);
	else
		sendmsg($socket, "Ho scelto la parola $word", $channel);
}

?>