<?php

require_once("funcs.php");

function addword($socket, $channel, $sender, $msg, $infos)
{
	global $db;

	$idbadword = verifica_badword($infos[1]);
	$idchan = $db->check_chan($channel);
	$db->insert("forbidden", array("IDChannel", "IDBadWord"), array($idchan, $idbadword));
	send($socket, "PRIVMSG $channel :Aggiunta la parola $infos[1] nella lista!!!\n");
}

?>