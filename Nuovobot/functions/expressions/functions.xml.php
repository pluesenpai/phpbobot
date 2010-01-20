<?php
	function expressions_generateXml($lang)
	{
		$langs["it_IT"] = array(
			"expression-regex" => "/^valuta (.+)$/",
			"expression-descr_name" => "valuta {ESPRESSIONE}",
			"expression-descr" => "Calcola il risultato di {ESPRESSIONE}."
		);

		$langs["en_GB"] = array(
			"expression-regex" => "/^eval (.+)$/",
			"expression-descr_name" => "eval {EXPRESSION}",
			"expression-descr" => "Evals {EXPRESSION}."
		);
		
		$xml = <<<EOF
<db>
	<function>
		<name>expression</name>
		<privileged>0</privileged>
		<regex><![CDATA[{$langs[$lang]["expression-regex"]}]]></regex>
		<descr_name>{$langs[$lang]["expression-descr_name"]}</descr_name>
		<descr><![CDATA[{$langs[$lang]["expression-descr"]}]]></descr>
		<tipo>normal</tipo>
	</function>
</db>

EOF;

		return $xml;
	}
?>