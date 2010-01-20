<?php

	function pasted($socket, $channel, $sender, $msg, $infos)
	{
		global $user_name, $dir_paste, $db, $paste_langs, $paste_langs1;

		if(file_exists("{$dir_paste}{$sender}_paste.txt")) {
			$db->update("user", array("paste_enabled"), array("false"), array("username"), array("="), array($sender));

			$post_sender = $user_name;

			$content = file("{$dir_paste}{$sender}_paste.txt");

			$lang =  str_replace(array("\n","\r"), "", $content[1]);
			$post_desc = str_replace(array("\n","\r"), "", $content[2]);
			unset($content[0], $content[1], $content[2]);
			$content = array_values($content);
			$post_text = implode('', $content);

			$index = array_search($lang, $paste_langs1);
			$post_language = $paste_langs[$index];

			unlink("{$dir_paste}{$sender}_paste.txt");

			$post_data = array(
				"lang" => $post_language,
				"nick" => $post_sender,
				"desc" => $post_desc,
				"text" => $post_text
			);

			print_r($post_data);

			sendmsg($socket, "$sender:: Attendi, sto eseguendo il paste...", $channel);

			$page = curl_init();
			curl_setopt($page, CURLOPT_URL, "http://rafb.net/paste/paste.php");
			curl_setopt($page, CURLOPT_REFERER, "http://rafb.net/paste/");
			curl_setopt($page, CURLOPT_POST, true);
			curl_setopt($page, CURLOPT_POSTFIELDS, $post_data);
			curl_setopt($page, CURLOPT_RETURNTRANSFER, true);
			curl_setopt($page, CURLOPT_FOLLOWLOCATION, true);
			$body = curl_exec($page);
			$link = curl_getinfo($page, CURLINFO_EFFECTIVE_URL);
			curl_close($page);

			echo $link;

			sendmsg($socket, "$sender:: Link del paste: \002\00302$link\00F", $channel);
		}
	}
?>