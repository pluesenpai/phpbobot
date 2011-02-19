<?php

	function autobpp($socket, $channel, $sender, $msg, $infos)
	{
		global $db, $translations;

		if(strtolower($infos[1]) == "on") {
			$db->update("chan", array("is_bpp_on"), array("true"), array("name"), array("="), array($channel));
			sendmsg($socket, $translations->bot_gettext("bpp-on"), $channel);
		} elseif(strtolower($infos[1]) == "off") {
			$db->update("chan", array("is_bpp_on"), array("false"), array("name"), array("="), array($channel));
			sendmsg($socket, $translations->bot_gettext("bpp-off"), $channel);
		}
	}

?>
