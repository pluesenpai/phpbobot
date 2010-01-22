<?php

//require_once("funcs.php");

function bigd($socket, $channel, $sender, $msg, $infos)
{
	global $translations;

	if(isset($infos[3]) && $infos[3] == "-comm")
		$pag = "commenti/comm" . $infos[4];
	else
		$pag = "storie/" . $infos[1] . "/story" . $infos[2];

	$address = "http://www.soft-land.org/$pag";

	$body = getpage($address);

	if(preg_match("/<title>Documento Non Trovato<\/title>/", $body)) {
		sendmsg($socket, $translations->bot_gettext("joking-bidg_notfound"), $channel); //"Spiacente. Non sono riuscito a trovarlo."
		return;
	}

	preg_match("/<title>(.+?)<\/title>/", $body, $result);

	$title = $result[1];

	sendmsg($socket, "BigD: \037\00302$title\037 \00301@ \002\00304$address\002", $channel, 1, true);
}

?>