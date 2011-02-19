<?php
	function greetuser($socket, $channel, $sender, $msg, $infos)
	{
		global $users, $user_name;
	
		if(preg_match("/\b([\+%&$~\@])*{$infos[1]}\b/", implode(" ", $users[$channel])) && $infos[1] != $user_name)
			sendmsg($socket, sprintf(_("greetuser-message-%s"), $infos[1]), $channel);
		elseif(count($infos) > 1 && $infos[1] == $user_name)
			sendmsg($socket, sprintf(_("greetuser-userbot-%s"), $infos[1]), $channel);
		else
			sendmsg($socket, sprintf(_("greetuser-absent-%s"), $infos[1]), $channel);
	}
?>
