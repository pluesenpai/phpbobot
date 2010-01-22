<?php

require_once("funcs.php");

function rmword($socket, $channel, $sender, $msg, $infos)
{
	global $bad_words,  $db, $translations;

	$ret = array_search($infos[1], $bad_words[$channel]);
	print_r($bad_words[$channel]);
	if($ret !== false) {
		$idchan = $db->check_chan($channel);
		$idbadword = verifica_badword($infos[1]);
		$db->remove("forbidden", array("IDChannel", "IDBadWord"), array("=", "="), array($idchan, $idbadword));
		sendmsg($socket, sprintf($translations->bot_gettext("moderating-rmword-removed-%s"), $infos[1]), $channel); //"Fatto!!! $infos[1] non &egrave; pi&ugrave; nella lista!!!"
	} else
		sendmsg($socket, sprintf($translations->bot_gettext("moderating-rmword-notpresent-%s"), $infos[1]), $channel); //"Spiacente... $infos[1] non era nella lista!!!"
}

?>