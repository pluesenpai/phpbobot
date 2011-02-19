<?php
	function definitions_generateXml($lang)
	{
		$langs["it_IT"] = array(
			"search-regex" => "/^\? (.+)$/",
			"search-descr_name" => "? {PAROLA O FRASE}",
			"search-descr" => "Cerco {PAROLA O FRASE} nel dizionario.",
			//------------------------------------------------------------
			"learn-regex" => "/^impara \"(.+?)\" (.+)$/",
			"learn-descr_name" => "impara {PAROLA O FRASE} {DEFINIZIONE}",
			"learn-descr" => "Aggiunge {PAROLA O FRASE} nel dizionario, con significato {DEFINIZIONE}.",
			//------------------------------------------------------------
			"randdef-regex" => "/^randdef$/",
			"randdef-descr_name" => "randdef",
			"randdef-descr" => "Mostra una definizione casuale dal dizionario.",
			//------------------------------------------------------------
			"forget-regex" => "/^dimentica (.+)$/",
			"forget-descr_name" => "dimentica {PAROLA O FRASE}",
			"forget-descr" => "Dimentica l'ultimo significato inserito di {PAROLA O FRASE}."
		);
		$langs["en_GB"] = array(
			"search-regex" => "/^\? (.+)$/",
			"search-descr_name" => "? {WORD OR PHRASE}",
			"search-descr" => "I'll search {WORD OR PHRASE} in the dictionary.",
			//------------------------------------------------------------
			"learn-regex" => "/^learn \"(.+?)\" (.+)$/",
			"learn-descr_name" => "learn {WORD OR PHRASE} {DEFINITON}",
			"learn-descr" => "Add {WORD OR PHRASE} in the dictionary, which means {DEFINITON}.",
			//------------------------------------------------------------
			"randdef-regex" => "/^randdef$/",
			"randdef-descr_name" => "randdef",
			"randdef-descr" => "Shows a random definition from the dictionary.",
			//------------------------------------------------------------
			"forget-regex" => "/^forget (.+)$/",
			"forget-descr_name" => "forget {WORD OR PHRASE}",
			"forget-descr" => "Forget the last inserted meaning of {WORD OR PHRASE}."
		);
		
		$xml = <<<EOF
<db>
	<function>
		<name>search</name>
		<privileged>0</privileged>
		<regex><![CDATA[{$langs[$lang]["search-regex"]}]]></regex>
		<descr_name>{$langs[$lang]["search-descr_name"]}</descr_name>
		<descr><![CDATA[{$langs[$lang]["search-descr"]}]]></descr>
		<tipo>normal</tipo>
	</function>
	<function>
		<name>learn</name>
		<privileged>0</privileged>
		<regex><![CDATA[{$langs[$lang]["learn-regex"]}]]></regex>
		<descr_name>{$langs[$lang]["learn-descr_name"]}</descr_name>
		<descr><![CDATA[{$langs[$lang]["learn-descr"]}]]></descr>
		<tipo>normal</tipo>
	</function>
	<function>
		<name>randdef</name>
		<privileged>0</privileged>
		<regex><![CDATA[{$langs[$lang]["randdef-regex"]}]]></regex>
		<descr_name>{$langs[$lang]["randdef-descr_name"]}</descr_name>
		<descr><![CDATA[{$langs[$lang]["randdef-descr"]}]]></descr>
		<tipo>normal</tipo>
	</function>
	<function>
		<name>forget</name>
		<privileged>0</privileged>
		<regex><![CDATA[{$langs[$lang]["forget-regex"]}]]></regex>
		<descr_name>{$langs[$lang]["forget-descr_name"]}</descr_name>
		<descr><![CDATA[{$langs[$lang]["forget-descr"]}]]></descr>
		<tipo>normal</tipo>
	</function>
</db>

EOF;

		return $xml;
	}
?>