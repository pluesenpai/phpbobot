<?php
	function fortune_generateXml($lang)
	{
		$langs["it_IT"] = array(
			"fortune-regex" => "/^fortune$/",
			"fortune-descr_name" => "fortune",
			"fortune-descr" => "Scriverò in canale uno dei mitici fortune ;) (potrebbe essere anche un fortune \"offensivo\")",
			//-----------------------------------------------------------
			"fortune_i-regex" => "/^fortune-i$/",
			"fortune_i-descr_name" => "fortune-i",
			"fortune_i-descr" => "Scriverò in canale uno dei mitici fortune ;) (non sarà un fortune \"offensivo\")",
			//-----------------------------------------------------------
			"fortune_o-regex" => "/^fortune-o$/",
			"fortune_o-descr_name" => "fortune-o",
			"fortune_o-descr" => "Scriverò in canale un fortune \"offensivo\".",
		);

		$langs["en_GB"] = array(
			"fortune-regex" => "/^fortune$/",
			"fortune-descr_name" => "fortune",
			"fortune-descr" => "I'll write a fortune in chan (it could be a \"offensive\" fortune, too).",
			//-----------------------------------------------------------
			"fortune_i-regex" => "/^fortune-i$/",
			"fortune_i-descr_name" => "fortune-i",
			"fortune_i-descr" => "I'll write a fortune in chan (it won't write \"offensive\" fortunes).",
			//-----------------------------------------------------------
			"fortune_o-regex" => "/^fortune-o$/",
			"fortune_o-descr_name" => "fortune-o",
			"fortune_o-descr" => "I'll write an \"offensive\" fortune in chan."
		);
		
		$xml = <<<EOF
<db>
	<function>
		<name>fortune</name>
		<privileged>0</privileged>
		<regex><![CDATA[{$langs[$lang]["fortune-regex"]}]]></regex>
		<descr_name>{$langs[$lang]["fortune-descr_name"]}</descr_name>
		<descr><![CDATA[{$langs[$lang]["fortune-descr"]}]]></descr>
		<tipo>normal</tipo>
	</function>
	<function>
		<name>fortune</name>
		<privileged>0</privileged>
		<regex><![CDATA[{$langs[$lang]["fortune_i-regex"]}]]></regex>
		<descr_name>{$langs[$lang]["fortune_i-descr_name"]}</descr_name>
		<descr><![CDATA[{$langs[$lang]["fortune_i-descr"]}]]></descr>
		<tipo>normal</tipo>
	</function>
	<function>
		<name>fortune</name>
		<privileged>0</privileged>
		<regex><![CDATA[{$langs[$lang]["fortune_o-regex"]}]]></regex>
		<descr_name>{$langs[$lang]["fortune_o-descr_name"]}</descr_name>
		<descr><![CDATA[{$langs[$lang]["fortune_o-descr"]}]]></descr>
		<tipo>normal</tipo>
	</function>
</db>

EOF;

		return $xml;
	}
?>