<?php

function listwords($socket, $channel, $sender, $msg, $infos)
{
	global $db, $translations;

	$idchan = $db->check_chan($channel);
	$bad_words = $db->select(
		array("bad_words", "forbidden"),
		array("word"),
		array(""),
		array("IDBadWord", "IDChannel"),
		array("=", "="),
		array("IDWord", $idchan)
	);

	$words = array();

	foreach($bad_words as $badword)
		$words[] = $badword['word'];

	if(count($words) > 0) {
		sendmsg($socket, $translations->bot_gettext("moderating-listwords"), $channel); //"Allora... Ti do l'elenco delle parole vietate!! ;)"
		sendmsg($socket, implode(", ", $words), $channel);
	} else
		sendmsg($socket, $translations->bot_gettext("moderating-nobadwords"), $channel); //"Non ci sono parole vietate in questo canale"
}

?>