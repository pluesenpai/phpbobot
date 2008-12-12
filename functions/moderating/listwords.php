<?php

function listwords($socket, $channel, $sender, $msg, $infos)
{
	global $bad_words;
	sendmsg($socket, "Allora... Ti do l'elenco delle parole vietate!! ;)", $channel);
	$testo_p = implode(" ", $bad_words);
	sendmsg($socket, "$testo_p", $channel);
}

?>