<?php
	function moderating_generateXml($lang)
	{
		$langs["it_IT"] = array(
			"addword-regex" => "/^\+(.*)$/",
			"addword-descr_name" => "+{PAROLA}",
			"addword-descr" => "Aggiungier&ograve; {PAROLA} alla lista della parole proibite.",
			//-------------------------------------------------
			"rmword-regex" => "/^-(.*)$/",
			"rmword-descr_name" => "-{PAROLA}",
			"rmword-descr" => "Rimuver&ograve; {PAROLA} dalla lista delle parole proibite.",
			//-------------------------------------------------
			"modera-regex" => "/^modera/",
			"modera-descr_name" => "modera",
			"modera-descr" => "Moderazione del canale attiva.",
			//-------------------------------------------------
			"stop-regex" => "/^stop$/",
			"stop-descr_name" => "stop",
			"stop-descr" => "Moderazione del canale disattivata.",
			//-------------------------------------------------
			"listwords-regex" => "/^proibite$/",
			"listwords-descr_name" => "proibite",
			"listwords-descr" => "Visualizzer&ograve; la lista delle parole proibite.",
			//-------------------------------------------------
			"info-regex" => "/^info$/",
			"info-descr_name" => "info",
			"info-descr" => "Verrai informato se modero o meno il canale."
		);

		$langs["en_GB"] = array(
			"addword-regex" => "/^\+(.*)$/",
			"addword-descr_name" => "+{WORD}",
			"addword-descr" => "It will be added {WORD} to the list of bad words.",
			//-------------------------------------------------
			"rmword-regex" => "/^-(.*)$/",
			"rmword-descr_name" => "-{WORD}",
			"rmword-descr" => "It will be removed {WORD} from the list of bad words.",
			//-------------------------------------------------
			"modera-regex" => "/^start$/",
			"modera-descr_name" => "start",
			"modera-descr" => "Typing modera the chan will be moderated by me.",
			//-------------------------------------------------
			"stop-regex" => "/^stop$/",
			"stop-descr_name" => "stop",
			"stop-descr" => "Typing stop I will stop moderating this chan.",
			//-------------------------------------------------
			"listwords-regex" => "/^listwords$/",
			"listwords-descr_name" => "listwords",
			"listwords-descr" => "Typing listwords I will show you all bad words.",
			//-------------------------------------------------
			"info-regex" => "/^info$/",
			"info-descr_name" => "info",
			"info-descr" => "Typing info I will inform you if I'm moderating this chan."
		);
		
		$xml = <<<EOF
<db>
	<function>
		<name>addword</name>
		<privileged>1</privileged>
		<regex><![CDATA[{$langs[$lang]["addword-regex"]}]]></regex>
		<descr_name>{$langs[$lang]["addword-descr_name"]}</descr_name>
		<descr><![CDATA[{$langs[$lang]["addword-descr"]}]]></descr>
		<tipo>normal</tipo>
	</function>
	<function>
		<name>rmword</name>
		<privileged>1</privileged>
		<regex><![CDATA[{$langs[$lang]["rmword-regex"]}]]></regex>
		<descr_name>{$langs[$lang]["rmword-descr_name"]}</descr_name>
		<descr><![CDATA[{$langs[$lang]["rmword-descr"]}]]></descr>
		<tipo>normal</tipo>
	</function>
	<function>
		<name>modera</name>
		<privileged>1</privileged>
		<regex><![CDATA[{$langs[$lang]["modera-regex"]}]]></regex>
		<descr_name>{$langs[$lang]["modera-descr_name"]}</descr_name>
		<descr><![CDATA[{$langs[$lang]["modera-descr"]}]]></descr>
		<tipo>normal</tipo>
	</function>
	<function>
		<name>stop</name>
		<privileged>1</privileged>
		<regex><![CDATA[{$langs[$lang]["stop-regex"]}]]></regex>
		<descr_name>{$langs[$lang]["stop-descr_name"]}</descr_name>
		<descr><![CDATA[{$langs[$lang]["stop-descr"]}]]></descr>
		<tipo>normal</tipo>
	</function>
	<function>
		<name>listwords</name>
		<privileged>0</privileged>
		<regex><![CDATA[{$langs[$lang]["listwords-regex"]}]]></regex>
		<descr_name>{$langs[$lang]["listwords-descr_name"]}</descr_name>
		<descr><![CDATA[{$langs[$lang]["listwords-descr"]}]]></descr>
		<tipo>normal</tipo>
	</function>
	<function>
		<name>info</name>
		<privileged>0</privileged>
		<regex><![CDATA[{$langs[$lang]["info-regex"]}]]></regex>
		<descr_name>{$langs[$lang]["info-descr_name"]}</descr_name>
		<descr><![CDATA[{$langs[$lang]["info-descr"]}]]></descr>
		<tipo>normal</tipo>
	</function>
	<function>
		<name>ver_words</name>
		<tipo>always</tipo>
	</function>
</db>

EOF;

		return $xml;
	}
?>