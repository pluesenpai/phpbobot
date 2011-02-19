<?php

function bigd($socket, $channel, $sender, $msg, $infos)
{
	global $translations;

	if(isset($infos[3]) && $infos[3] == "-comm") {
		$pagine[] = sprintf("commenti/%03d", $infos[4]);
		$pagine[] = sprintf("commenti/comm%03d", $infos[4]);
	} elseif(isset($infos[5]) && $infos[5] == "-visit") {
		//$pag = "storie/visit/" . $infos[6];
		$pagine[] = sprintf("storie/visit/%03d", $infos[6]);
		$pagine[] = sprintf("storie/visit/visit%03d", $infos[6]);
	} else {
		//$pag = "storie/" . $infos[1] . "/story" . $infos[2];
		$pagine[] = sprintf("storie/%02d/story%02d", $infos[1], $infos[2]);
	}

	$found = false;
	$i = 0;
	while($i < count($pagine) && $found != true) {
		$pag = $pagine[$i];
		$address = "http://www.soft-land.org/$pag";

		$body = getpage($address);

		if(preg_match("/<title>Documento Non Trovato<\/title>/", $body)) {
			sendmsg($socket, $translations->bot_gettext("joking-bidg_notfound"), $channel); //"Spiacente. Non sono riuscito a trovarlo."
			return;
		} else {
			$found = true;

			preg_match("/<title>(.+?)<\/title>/", $body, $result);

			$title = $result[1];

			sendmsg($socket, "BigD: " . IRCColours::UNDERLINE . IRCColours::BLUE . $title . IRCColours::Z . " @ " . IRCColours::BOLD . IRCColours::RED . $address . IRCColours::Z, $channel, 1, true);
		}

		$i++;
	}
	
	if($found == false)
		sendmsg($socket, $translations->bot_gettext("joking-bidg_notfound"), $channel); //"Spiacente. Non sono riuscito a trovarlo."
}

?>
