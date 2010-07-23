<?php
	function youtube($socket, $channel, $sender, $msg, $infos)
	{
		global $translations;
	
		$always = preg_match("/always/", $infos[0]);
		if($always) {
			$messaggio = explode(" ", $msg);
			$q = $messaggio[0];
		} else
			$q = $infos[2];

		if(preg_match("#^http://www\.youtube\.com/(.+)\?(.+)$#", $q, $results)) {
			$querystring = explode("&", $results[2]);
			$index = array_search("v=", $querystring);
			$body = getpage($q);
			$id = substr($querystring[$index], 2);
		} elseif(!$always) {
			$body = getpage("http://www.youtube.com/watch?v=$q");
			$id = $q;
		}

		if(isset($id)) {
			preg_match("/<meta name=\"title\" content=\"(.+?)\">/", $body, $result);

			if(count($result[0]) == 0)
				sendmsg($socket, $translations->bot_gettext("youtube-notfound"), $channel); //"Spiacente. Non ho trovato alcun risultato"
			else {
				$title = html_entity_decode(htmlentities($result[1], ENT_QUOTES, 'UTF-8'));
				$address = "http://www.youtube.com/watch?v=$id";
				//sendmsg($socket, "Video: \037\00302$title\037 \00301@ \002\00304$address\002", $channel, 1, true);
				sendmsg($socket, "Video: " . IRCColours::UNDERLINE . IRCColours::BLUE . $title . IRCColours::UNDERLINE . IRCColours::Z_COLOUR . " @ " . IRCColours::BOLD . IRCColours::RED . $address . IRCColours::Z, $channel, 1, true);
			}
		}
	}
?>