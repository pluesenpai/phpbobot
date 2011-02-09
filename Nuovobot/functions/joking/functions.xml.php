<?php
	function joking_generateXml($lang)
	{
		$langs["it_IT"] = array(
			"cumbidu-regex" => "/^cumbida$/",
			"cumbidu-descr_name" => "cumbida",
			"cumbidu-descr" => "Cumbidu po tottusu :)",
			//------------------------------------------------
			"inviteto-regex" => "/^offri (.+?) a (.+)$/",
			"inviteto-descr_name" => "offri {QUALCOSA} a {UTENTE}",
			"inviteto-descr" => "Offre quello che vuoi a {USER} :)",
			//------------------------------------------------
			"invite-regex" => "/^hai (.+?)\?$/",
			"invite-descr_name" => "hai {SOMETHING}?",
			"invite-descr" => "Ti offre quello che vuoi :)",
			//------------------------------------------------
			"roulette-regex" => "/^roulette (.+( [ ]*.+)*)$/",
			"roulette-descr_name" => "roulette {WORD1}[ {WORD2}[ {WORD3}...]]",
			"roulette-descr" => "Sceglierò tra una delle parole scelte.",
			//------------------------------------------------
			"lotteria-regex" => "/^lotteria ([0-9]+) ([0-9]+)$/",
			"lotteria-descr_name" => "lotteria {MIN} {MAX}",
			"lotteria-descr" => "Sceglierò un numero casuale tra {MIN} e {MAX}",
			//------------------------------------------------
			"dado-regex" => "/^dado ([0-9]+)$/",
			"dado-descr_name" => "dado {N_DADI}",
			"dado-descr" => "Tirerò {N_DADI} dadi.",
			//------------------------------------------------
			"bigd-regex" => "/^bigd ([0-9]+) ([0-9]+)$|^bigd (-comm){1} ([0-9]+)$|^bigd (-visit){1} ([0-9]+)$/",
			"bigd-descr_name" => "bigd {AA} {SS} / bigd -comm {NUM} / bigd -visit {NUM}",
			"bigd-descr" => "Mostrerò il titolo della storia di BigD dell'anno {AA} e settimana {SS} o il commento numero {NUM} o la storia degli ospiti numero {NUM}."
		);

		$langs["en_GB"] = array(
			"cumbidu-regex" => "/^cumbida$/",
			"cumbidu-descr_name" => "cumbida",
			"cumbidu-descr" => "Cumbidu po tottusu :)",
			//------------------------------------------------
			"inviteto-regex" => "/^a (.+?) to (.+) please$/",
			"inviteto-descr_name" => "a {SOMETHING} to {USER}",
			"inviteto-descr" => "Give what you want to {USER} :)",
			//------------------------------------------------
			"invite-regex" => "/^a (.+?) please$/",
			"invite-descr_name" => "a {SOMETHING} please",
			"invite-descr" => "Gives you what you want :)",
			//------------------------------------------------
			"roulette-regex" => "/^roulette (.+( [ ]*.+)*)$/",
			"roulette-descr_name" => "roulette {WORD1}[ {WORD2}[ {WORD3}...]]",
			"roulette-descr" => "I'll choose one of given words",
			//------------------------------------------------
			"lotteria-regex" => "/^lottery ([0-9]+) ([0-9]+)$/",
			"lotteria-descr_name" => "lottery {MIN} {MAX}",
			"lotteria-descr" => "I'll pick a random number between {MIN} and {MAX}",
			//------------------------------------------------
			"dado-regex" => "/^dice ([0-9]+)$/",
			"dado-descr_name" => "dice {N_DICES}",
			"dado-descr" => "I'll pick the result from {N_DICES} dices.",
			//------------------------------------------------
			"bigd-regex" => "/^bigd ([0-9]+) ([0-9]+)$|^bigd (-comm){1} ([0-9]+)$|^bigd (-visit){1} ([0-9]+)$/",
			"bigd-descr_name" => "bigd {YY} {WW} / bigd -comm {NUM} / bigd -visit {NUM}",
			"bigd-descr" => "I'll choose BigD's story from year {YY} and week {WW} or comment numbered {NUM} or guest story number {NUM}"
		);
		
		$xml = <<<EOF
<db>
	<function>
		<name>cumbidu</name>
		<privileged>0</privileged>
		<regex><![CDATA[{$langs[$lang]["cumbidu-regex"]}]]></regex>
		<descr_name>{$langs[$lang]["cumbidu-descr_name"]}</descr_name>
		<descr><![CDATA[{$langs[$lang]["cumbidu-descr"]}]]></descr>
		<tipo>normal</tipo>
	</function>
	<function>
		<name>inviteto</name>
		<privileged>0</privileged>
		<regex><![CDATA[{$langs[$lang]["inviteto-regex"]}]]></regex>
		<descr_name>{$langs[$lang]["inviteto-descr_name"]}</descr_name>
		<descr><![CDATA[{$langs[$lang]["inviteto-descr"]}]]></descr>
		<tipo>normal</tipo>
	</function>
	<function>
		<name>invite</name>
		<privileged>0</privileged>
		<regex><![CDATA[{$langs[$lang]["invite-regex"]}]]></regex>
		<descr_name>{$langs[$lang]["invite-descr_name"]}</descr_name>
		<descr><![CDATA[{$langs[$lang]["invite-descr"]}]]></descr>
		<tipo>normal</tipo>
	</function>
	<function>
		<name>roulette</name>
		<privileged>0</privileged>
		<regex><![CDATA[{$langs[$lang]["roulette-regex"]}]]></regex>
		<descr_name>{$langs[$lang]["roulette-descr_name"]}</descr_name>
		<descr><![CDATA[{$langs[$lang]["roulette-descr"]}]]></descr>
		<tipo>normal</tipo>
	</function>
	<function>
		<name>dado</name>
		<privileged>0</privileged>
		<regex><![CDATA[{$langs[$lang]["dado-regex"]}]]></regex>
		<descr_name>{$langs[$lang]["dado-descr_name"]}</descr_name>
		<descr><![CDATA[{$langs[$lang]["dado-descr"]}]]></descr>
		<tipo>normal</tipo>
	</function>
	<function>
		<name>lotteria</name>
		<privileged>0</privileged>
		<regex><![CDATA[{$langs[$lang]["lotteria-regex"]}]]></regex>
		<descr_name>{$langs[$lang]["lotteria-descr_name"]}</descr_name>
		<descr><![CDATA[{$langs[$lang]["lotteria-descr"]}]]></descr>
		<tipo>normal</tipo>
	</function>
	<function>
		<name>bigd</name>
		<privileged>0</privileged>
		<regex><![CDATA[{$langs[$lang]["bigd-regex"]}]]></regex>
		<descr_name>{$langs[$lang]["bigd-descr_name"]}</descr_name>
		<descr><![CDATA[{$langs[$lang]["bigd-descr"]}]]></descr>
		<tipo>normal</tipo>
	</function>
</db>

EOF;

		return $xml;
	}
?>