<?php

require_once("funcs.php");

function rmword($socket, $channel, $sender, $msg, $infos)
{
	global $bad_words;
	global $db;
	$ret = array_search($infos[1], $bad_words[$channel]);
	print_r($bad_words[$channel]);
	if($ret !== false) {
// 		unset($bad_words[$ret]);
// 		$bad_words = array_values($bad_words);
// 		file_put_contents("functions/moderating/bad_words.txt", implode("\n", $bad_words), LOCK_EX);
		$idchan = $db->verifica_chan($channel);
		$idbadword = verifica_badword($infos[1]);
		$db->remove("proibita", array("IDChannel", "IDBadWord"), array("=", "="), array($idchan, $idbadword));
		sendmsg($socket, "Fatto!!! $infos[1] non &egrave; pi&ugrave; nella lista!!!", $channel);
	} else
		sendmsg($socket, "Spiacente... $infos[1] non era nella lista!!!", $channel);
}

?>