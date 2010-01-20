<?php
	function ver_words($socket, $channel, $sender, $msg, $infos)
	{
		global $operators, $bad_words, $moderated, $debug, $max_kicks, $db;

		if($infos[1] != "PRIVMSG")
			return;

		if($channel{0} != "#")
			return;

		$words = $bad_words[$channel];

		if($moderated[$channel]) {
			$array = explode(" ", $msg);
			foreach($array as $parola) {
				if(!in_array($sender, $operators) && array_search($parola, $words) !== FALSE) {
					sendmsg($socket, "$sender::: La parola $parola non &egrave; ammessa!!!", $channel, 0, true);
					$iduser = $db->check_user($sender);
					$idchan = $db->check_chan($channel);
					//SELECT kicks FROM entra WHERE IDUser = $iduser AND IDChan = $idchan
					$result = $db->select(
						array("entra"),
						array("kicks"),
						array(""),
						array("IDUser", "IDChan"),
						array("=", "="),
						array($iduser, $idchan)
					);
					if(count($result) == 0) {
						//INSERT INTO entra (IDUser, IDChan, IDSaluto, modes, kicks) VALUES ($iduser, $idchan, 0, "", 0)
						$db->insert("enter", array("iduser", "idchannel", "idgreet", "modes", "kicks"), array($iduser, $idchan, 0, "", 0));
						$kicks = 1;
					} else
						$kicks = ((int)$result[0]['kicks']) + 1;
					sendmsg($socket, "$sender::: E siamo a $kicks...", $channel, 0, true);
					if($kicks == $max_kicks - 1)
						sendmsg($socket, "$sender::: Alla prossima ti butto FUORI!!!", $channel, 0, true);
					elseif($kicks == $max_kicks) {
						sendmsg($socket, "$sender::: Ti avevo avertito!!!", $channel, 0, true);
						sendmsg($socket, "$sender::: FFUUOORRIIIIIII!!!", $channel, 0, true);
						send($socket, "KICK $channel $sender\n");
						send($socket, "MODE $channel +b $sender!*@*\n");
						sendmsg($socket, "Peggio per lui... Si arrangia!!! MWAHAHAHAH ;)", 0, true);
						$kicks = 0;
					}
					if($kicks < $max_kicks)
						send($socket, "KICK $channel $sender\n");
					//UPDATE entra SET kicks = $kicks WHERE IDUser = $iduser AND IDChan = $idchan
					$db->update("enter", array("kicks"), array($kicks), array("iduser", "idchannel"), array("=", "="), array($iduser, $idchan));
					//SELECT count FROM bad_words WHERE word = '$parola'
					$result = $db->select(
						array("bad_words"),
						array("count"),
						array(""),
						array("word"),
						array("="),
						array("'$parola'")
					);
					$count = ((int)$result[0]['count']) + 1;
					//UPDATE bad_words SET count = $count WHERE word = '$parola'
					$db->update("bad_words", array("count"), array($count), array("word"), array("="), array($parola));
				}
			}
		}
	}
?>