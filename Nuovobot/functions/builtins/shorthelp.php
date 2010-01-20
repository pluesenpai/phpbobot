<?php
	require_once("_help.php");

	function shorthelp($socket, $channel, $sender, $msg, $infos)
	{
		_help($sender, true);
	}
?>
