<?php
	function birthday_generateXml($lang)
	{
		$langs["it_IT"] = array(
			"set_birthday-regex" => "/^compleanno ([0-9]{1,2}) ([0-9]{1,2})$/",
			"set_birthday-descr_name" => "compleanno {GG} {MM}",
			"set_birthday-descr" => "Imposta la data di compleanno"
		);

		$langs["en_GB"] = array(
			"set_birthday-regex" => "/^setbirthday ([0-9]{1,2}) ([0-9]{1,2})$/",
			"set_birthday-descr_name" => "setbirthday {DD} {MM}",
			"set_birthday-descr" => "Sets your birthday."
		);
		
		$xml = <<<EOF
<db>
	<function>
		<name>set_birthday</name>
		<privileged>0</privileged>
		<regex><![CDATA[{$langs[$lang]["set_birthday-regex"]}]]></regex>
		<descr_name>{$langs[$lang]["set_birthday-descr_name"]}</descr_name>
		<descr><![CDATA[{$langs[$lang]["set_birthday-descr"]}]]></descr>
		<tipo>normal</tipo>
	</function>
	<function>
		<name>birthday</name>
		<tipo>join</tipo>
	</function>
</db>

EOF;

		return $xml;
	}
?>