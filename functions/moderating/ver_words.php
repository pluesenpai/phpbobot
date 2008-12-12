<?php
	function ver_words($socket, $channel, $sender, $msg, $infos)
	{
		global $operators;
		global $bad_words;
		global $debug;
		global $kickkati;	///NOTE: Attenzione!! Non è possibile modificare questa variabile direttamente.
							///NOTE: Il processo padre non si accorgerebbe del cambiamento. Occorre sostituire con il database!!
		global $max_kicks;

		if($moderated) {
			$array = explode(" ", $msg);
			for($j = 3; $j < count($array); $j++) {
				$parola = strtolower($array[$j]);
				dbg($debug, "Parola in posizione $j: $parola");
				if(!in_array($sender, $operators) && array_search($parola, $bad_words) !== false) {
					sendmsg($socket, "$sender::: La parola $parola non &egrave; ammessa!!!", $channel);
					sendmsg($socket, "$sender::: Ti prendo a calci!!!", $channel);
					send($socket, "KICK $channel $sender\n");
					$kickkati[$sender]++;
					dbg($debug, "Kick: $sender: $kickkati[$sender]");
				}
				if(array_search($parola, $bad_words) !== FALSE && !in_array($sender, $operators)) {
					sendmsg($socket, "$sender::: La parola $parola non &egrave; ammessa!!!", $channel);
					sendmsg($socket, "$sender::: E siamo a $kickkati[$sender]...", $channel);
					$kickkati[$sender]++;
					if($kickkati[$sender] == $max_kicks - 1)
						sendmsg($socket, "$sender::: Alla prossima ti butto FUORI!!!", $channel);
					elseif($kickkati[$sender] == $max_kicks) {
						sendmsg($socket, "$sender::: Ti avevo avertito!!!", $channel);
						sendmsg($socket, "$sender::: FFUUOORRIIIIIII!!!", $channel);
						sendmsg($socket, "MODE $channel +b $sender!*@*\n");
						sendmsg($socket, "Peggio per lui... Si arrangia!!! AHAHAHAH ;)");
						$kickkati[$sender] = 0;
					}
				}
			}
		}
	}
?>