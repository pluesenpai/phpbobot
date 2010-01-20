<?php
	function modes($socket, $channel, $sender, $msg, $infos)
	{
		if(preg_match("/^de/", $infos[0]))
			$mode = "-";
		else
			$mode = "+";
		if(preg_match("/halfop (.+)$/", $infos[0]))
			$mode .= str_repeat("h", substr_count($infos[1], " ") + 1);
		elseif(preg_match("/voice (.+)$/", $infos[0]))
			$mode .= str_repeat("v", substr_count($infos[1], " ") + 1);
		else
			$mode .= str_repeat("o", substr_count($infos[1], " ") + 1);

		send($socket, "MODE $channel $mode {$infos[1]}\n");
	}
?>
