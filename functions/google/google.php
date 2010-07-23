<?php
	function google($socket, $channel, $sender, $msg, $infos)
	{
		global $translations;

		$q = $infos[2];

		$body = getpage("http://www.google.it/search?q=$q");
		preg_match_all("/<!--m-->(.+?)<!--n-->/", $body, $result);

		if(count($result[0]) == 0)
			sendmsg($socket, $translations->bot_gettext("google-noresult"), $channel); //"Spiacente. Non ho trovato alcun risultato"

		foreach($result[0] as $r) {
			preg_match("#^<!--m--><li class=g><h3 class=r><a href=\"(.+?)\" class=l onmousedown=\"return clk\(this\.href,'','','res','[0-9]+','','[a-zA-Z0-9]+'\)\">(.+?)</a></h3><div class=\"s\">(.+)<br>#", $r, $result1);

			$result1 = preg_replace("#<em>(.+?)</em>#", "$1", $result1);
			$result1 = preg_replace("#<b>(.+?)</b>#", "$1", $result1);

			if(count($result1) > 0) {
				$address = html_entity_decode(htmlentities($result1[1], ENT_QUOTES, 'UTF-8'));
				$name = html_entity_decode(htmlentities($result1[2], ENT_QUOTES, 'UTF-8'));
				$address = preg_replace("#&bull;#", "-", $address);
				$name = preg_replace("#&bull;#", "-", $name);
				sendmsg($socket, "$name @ " . IRCColours::BOLD  . IRCColours::RED . $address . IRCColours::Z, $channel, 1, true);
			}
		}
	}
?>