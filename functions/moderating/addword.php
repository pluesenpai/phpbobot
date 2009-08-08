<?php

require_once("funcs.php");

function addword($socket, $channel, $sender, $msg, $infos)
{
// 	file_put_contents("functions/moderating/bad_words.txt", "\n$infos[1]", FILE_APPEND + LOCK_EX);
	//array_push($bad_words, $infos[1]);

	global $db;

	$idbadword = verifica_badword($infos[1]);
	$idchan = $db->verifica_chan($channel);
	$db->insert("proibita", array("IDChannel", "IDBadWord"), array($idchan, $idbadword));
	send($socket, "PRIVMSG $channel :Aggiunta la parola $infos[1] nella lista!!!\n");
}

?>