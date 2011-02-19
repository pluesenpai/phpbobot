<?php
	require_once("_help.php");

	function grouphelp($socket, $channel, $sender, $msg, $infos)
	{
		_help($sender, false, $infos[1]);
	}
?>
