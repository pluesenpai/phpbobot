<?php

function roulette($socket, $channel, $sender, $msg, $infos)
{
	global $users, $translations;

	$words = explode(",", $infos[1]);
	$word = trim($words[rand(0, count($words) - 1)]);

	//echo $channel . " " . $sender;

	if($channel != $sender && is_user_in_chan($word, $channel))
		sendmsg($socket, sprintf($translations->bot_gettext("joking-roulette_user-%s")), $channel); //"$word, sei stato nominato... ca\$\$i tuoi!"
	else
		sendmsg($socket, sprintf($translations->bot_gettext("joking-roulette_word-%s")), $channel); //"Ho scelto la parola $word"
}

?>