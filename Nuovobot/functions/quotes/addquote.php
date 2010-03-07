<?php

function addquote($socket, $channel, $sender, $msg, $infos)
{
	global $db, $translations;

	preg_match("/^<?(.+?)>?$/", $infos[1], $user);

	$id_poster = $db->check_user(clean_username($user[1]));
	$id_sender = $db->check_user($sender);
	$id_chan = $db->check_chan($channel);
	$message = $infos[2];

	$id_post = $db->insert("quotes", array("message", "sender", "poster", "channel"), array($message, $id_sender, $id_poster, $id_chan));

	sendmsg($socket, sprintf($translations->bot_gettext("quotes-addquote-ok-%s-%s"), $sender, $id_post), $channel); //"$sender, ho aggiunto la quote n. {$id_post}!!!"
}

?>
