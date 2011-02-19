<?php
	function bpp_generateXml($lang)
	{
		$langs["it_IT"] = array(
			"autobpp-regex" => "/^autobpp (on|off)$/",
			"autobpp-descr_name" => "autobpp ON|OFF",
			"autobpp-descr" => "Attiva o disattiva il parser delle variabili.",
			//------------------------------------------------
			"bpp-regex" => "/^bpp \\$([a-zA-Z_][a-zA-Z0-9_]*)$/",
			"bpp-descr_name" => "bpp \$VARIABILE",
			"bpp-descr" => "Stampa il valore di \$VARIABILE.",
			//------------------------------------------------
			"addvar-regex" => "/^var \\$([a-zA-Z_][a-zA-Z0-9_]*)[ ]*=[ ]*(.+)$/",
			"addvar-descr_name" => "var \$VARIABILE = VALORE",
			"addvar-descr" => "Crea una nuova variabile \$VARIABILE e gli assegna il valore VALORE.",
			//------------------------------------------------
			"delvar-regex" => "/^unset \\$([a-zA-Z_][a-zA-Z0-9_]*)$/",
			"delvar-descr_name" => "unset \$VARIABILE",
			"delvar-descr" => "Elimina la variabile \$VARIABILE."
		);

		$langs["en_GB"] = array(
			"autobpp-regex" => "/^autobpp (on|off)$/",
			"autobpp-descr_name" => "autobpp ON|OFF",
			"autobpp-descr" => "Enables or disables variables parsing.",
			//------------------------------------------------
			"bpp-regex" => "/^bpp \\$([a-zA-Z_][a-zA-Z0-9_]*)$/",
			"bpp-descr_name" => "bpp \$VARIABLE",
			"bpp-descr" => "Prints value of \$VARIABLE.",
			//------------------------------------------------
			"addvar-regex" => "/^var \\$([a-zA-Z_][a-zA-Z0-9_]*)[ ]*=[ ]*(.+)$/",
			"addvar-descr_name" => "var \$VARIABLE = VALUE",
			"addvar-descr" => "Create a new variable \$VARIABLE with value VALUE.",
			//------------------------------------------------
			"delvar-regex" => "/^unset \\$([a-zA-Z_][a-zA-Z0-9_]*)$/",
			"delvar-descr_name" => "unset \$VARIABLE",
			"delvar-descr" => "Deletes variable \$VARIABILE."
		);
		
		$xml = <<<EOF
<db>
	<function>
		<name>autobpp</name>
		<privileged>1</privileged>
		<regex><![CDATA[{$langs[$lang]["autobpp-regex"]}]]></regex>
		<descr_name>{$langs[$lang]["autobpp-descr_name"]}</descr_name>
		<descr><![CDATA[{$langs[$lang]["autobpp-descr"]}]]></descr>
		<tipo>normal</tipo>
	</function>
	<function>
		<name>bpp</name>
		<privileged>0</privileged>
		<regex><![CDATA[{$langs[$lang]["bpp-regex"]}]]></regex>
		<descr_name>{$langs[$lang]["bpp-descr_name"]}</descr_name>
		<descr><![CDATA[{$langs[$lang]["bpp-descr"]}]]></descr>
		<tipo>normal</tipo>
	</function>
	<function>
		<name>addvar</name>
		<privileged>1</privileged>
		<regex><![CDATA[{$langs[$lang]["addvar-regex"]}]]></regex>
		<descr_name>{$langs[$lang]["addvar-descr_name"]}</descr_name>
		<descr><![CDATA[{$langs[$lang]["addvar-descr"]}]]></descr>
		<tipo>normal</tipo>
	</function>
	<function>
		<name>delvar</name>
		<privileged>1</privileged>
		<regex><![CDATA[{$langs[$lang]["delvar-regex"]}]]></regex>
		<descr_name>{$langs[$lang]["delvar-descr_name"]}</descr_name>
		<descr><![CDATA[{$langs[$lang]["delvar-descr"]}]]></descr>
		<tipo>normal</tipo>
	</function>
	<function>
		<name>bpp</name>
		<tipo>always</tipo>
	</function>
</db>

EOF;

		return $xml;
	}
?>