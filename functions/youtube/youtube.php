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

	function youtube($socket, $channel, $sender, $msg, $infos)
	{
		$q = $infos[1];

		$body = getpage("http://www.youtube.com/watch?v=$q");

		preg_match("/<meta name=\"title\" content=\"(.+?)\">/", $body, $result);

		if(count($result[0]) == 0)
			sendmsg($socket, "Spiacente. Non ho trovato alcun risultato", $channel);
		else {
			$title = html_entity_decode(htmlentities($result[1], ENT_QUOTES, 'UTF-8'));
			$address = "http://www.youtube.com/watch?v=" . $q;
			sendmsg($socket, "Video: $title @ \002\00304$address\00F", $channel, 1, true);
		}
	}
?>