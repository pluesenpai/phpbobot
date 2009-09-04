<?php
	/* \002 BOLD
	 * \037 UNDERLINE
	 * \003XY COLOR
	 * X:
	 *  00 LIGHTGRAY
	 *  01 BLACK
	 *  02 BLUE
	 *  03 GREEN
	 *  04 RED
	 *  05 ROSSO MATTONE
	 *  06 PURPLE
	 *  07 MAROON
	 *  08 ORANGE
	 *  09 LIGHTGREEN
	 *  10 TEAL
	 *  11 AQUA
	 *  12 ROYAL
	 *  13 VIOLET
	 *  14 DARKGRAY
	 *  15 GRAY / SILVER
	 *  99 TRANSPARENT
	 */
	include("funcs.php");

	function google($socket, $channel, $sender, $msg, $infos)
	{
		$q = $infos[2];

		$body = getpage("http://www.google.it/search?q=$q");
		preg_match_all("/<!--m-->(.+?)<!--n-->/", $body, $result);

		if(count($result[0]) == 0)
			sendmsg($socket, "Spiacente. Non ho trovato alcun risultato", $channel);

		foreach($result[0] as $r) {
			preg_match("#^<!--m--><li class=g><h3 class=r><a href=\"(.+?)\" class=l onmousedown=\"return clk\(this\.href,'','','res','[0-9]+',''\)\">(.+?)</a></h3><div class=\"s\">(.+)<br>#", $r, $result1);

			$result1 = preg_replace("#<em>(.+?)</em>#", "$1", $result1);
			$result1 = preg_replace("#<b>(.+?)</b>#", "$1", $result1);

			if(count($result1) > 0) {
				$address = html_entity_decode(htmlentities($result1[1], ENT_QUOTES, 'UTF-8'));
				$name = html_entity_decode(htmlentities($result1[2], ENT_QUOTES, 'UTF-8'));
				$address = preg_replace("#&bull;#", "-", $address);
				$name = preg_replace("#&bull;#", "-", $name);
				sendmsg($socket, "$name @ \002\00304$address\002", $channel, 1, true);
			}
		}
	}
?>