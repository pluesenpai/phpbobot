<?php
	function operators_generateXml($lang)
	{
		$langs["it_IT"] = array(
			"addop-regex" => "/^>(.*)$/",
			"addop-descr_name" => "<![CDATA[>{OPERATORE}]]>",
			"addop-descr" => "Permette di aggiungere {OPERATORE} nella lista dei bot-operatori.",
			//-----------------------------------------------------------------
			"rmop-regex" => "/^<(.*)$/",
			"rmop-descr_name" => "<![CDATA[<{OPERATORE}]]>",
			"rmop-descr" => "Permette di rimuovere {OPERATORE} dalla lista dei bot-operatori.",
			//-----------------------------------------------------------------
			"listop-regex" => "/^listop$/",
			"listop-descr_name" => "listop",
			"listop-descr" => "Mostro la lista dei bot-operatori."
		);

		$langs["en_GB"] = array(
			"addop-regex" => "/^>(.*)$/",
			"addop-descr_name" => "![CDATA[>{OPERATOR}]]>",
			"addop-descr" => "Typing >{OPERATOR} I'll add {OPERATOR} in the bot operators list.",
			//-----------------------------------------------------------------
			"rmop-regex" => "/^<(.*)$/",
			"rmop-descr_name" => "<![CDATA[<{OPERATOR}]]>",
			"rmop-descr" => "Typing <{OPERATOR} I'll remove {OPERATOR} from the bot operators list.",
			//-----------------------------------------------------------------
			"listop-regex" => "/^listop$/",
			"listop-descr_name" => "listop",
			"listop-descr" => "Typing listop I'll show you all bot operators."
		);
		
		$xml = <<<EOF
<db>
	<function>
		<name>addop</name>
		<privileged>1</privileged>
		<regex><![CDATA[{$langs[$lang]["addop-regex"]}]]></regex>
		<descr_name>{$langs[$lang]["addop-descr_name"]}</descr_name>
		<descr><![CDATA[{$langs[$lang]["addop-descr"]}]]></descr>
		<tipo>normal</tipo>
	</function>
	<function>
		<name>rmop</name>
		<privileged>1</privileged>
		<regex><![CDATA[{$langs[$lang]["rmop-regex"]}]]></regex>
		<descr_name>{$langs[$lang]["rmop-descr_name"]}</descr_name>
		<descr><![CDATA[{$langs[$lang]["rmop-descr"]}]]></descr>
		<tipo>normal</tipo>
	</function>
	<function>
		<name>listop</name>
		<privileged>0</privileged>
		<regex><![CDATA[{$langs[$lang]["listop-regex"]}]]></regex>
		<descr_name>{$langs[$lang]["listop-descr_name"]}</descr_name>
		<descr><![CDATA[{$langs[$lang]["listop-descr"]}]]></descr>
		<tipo>normal</tipo>
	</function>
</db>

EOF;

		return $xml;
	}
?>