<?php
	require_once("_help.php");

	function help($socket, $channel, $sender, $msg, $infos)
	{
		//_help($sender, false);
		_help($sender, true);
	}
?>
