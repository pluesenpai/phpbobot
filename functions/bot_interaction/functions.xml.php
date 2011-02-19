<?php
	function bot_interaction_generateXml($lang)
	{
		$langs["it_IT"] = array(
			"ansa-regex" => "/^ansa ([0-9]+)$/",
			"ansa-descr_name" => "ansa {NUMERO}",
			"ansa-descr" => "Restituisce la notizia ansa numero {NUMERO}"
		);

		$langs["en_GB"] = array(
			"ansa-regex" => "/^ansa ([0-9]+)$/",
			"ansa-descr_name" => "ansa {NUMBER}",
			"ansa-descr" => "Gets ansa new number {NUMBER}"
		);
		
		$xml = <<<EOF
<db>
	<function>
		<name>ansa</name>
		<privileged>0</privileged>
		<regex><![CDATA[{$langs[$lang]["ansa-regex"]}]]></regex>
		<descr_name>{$langs[$lang]["ansa-descr_name"]}</descr_name>
		<descr><![CDATA[{$langs[$lang]["ansa-descr"]}]]></descr>
		<tipo>normal</tipo>
	</function>
	<function>
		<name>bot_ansa</name>
		<tipo>always</tipo>
	</function>
</db>

EOF;

		return $xml;
	}
?>