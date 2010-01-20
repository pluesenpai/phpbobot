<?php
	function paste_generateXml($lang)
	{
		$langs["it_IT"] = array(
			"startpaste-regex" => "/^paste (.+?) (.+)$/",
			"startpaste-descr_name" => "paste {LANG} {DESCR}",
			"startpaste-descr" => "Mi preparo per memorizzare codice di cui fare il paste su http://rafb.net/paste/.",
			//------------------------------------
			"pasted-regex" => "/^pasted$/",
			"pasted-descr_name" => "pasted",
			"pasted-descr" => "Faccio il paste del codice fornito su http://rafb.net/paste/.",
			//------------------------------------
			"pastetypes-regex" => "/^pastetypes$/",
			"pastetypes-descr_name" => "pastetypes",
			"pastetypes-descr" => "Mostro i linguaggi supportati da http://rafb.net/paste/."
		);

		$langs["en_GB"] = array(
			"startpaste-regex" => "/^paste (.+?) (.+)$/",
			"startpaste-descr_name" => "paste {LANG} {DESCR}",
			"startpaste-descr" => "I'll prepare for memorize code to paste to http://rafb.net/paste/.",
			//------------------------------------
			"pasted-regex" => "/^pasted$/",
			"pasted-descr_name" => "pasted",
			"pasted-descr" => "I'll paste the code you gave to me to http://rafb.net/paste/.",
			//------------------------------------
			"pastetypes-regex" => "/^pastetypes$/",
			"pastetypes-descr_name" => "pastetypes",
			"pastetypes-descr" => "I'll show you what languages are supported by http://rafb.net/paste/."
		);
		
		$xml = <<<EOF
<db>
	<function>
		<name>startpaste</name>
		<privileged>0</privileged>
		<regex><![CDATA[{$langs[$lang]["startpaste-regex"]}]]></regex>
		<descr_name>{$langs[$lang]["startpaste-descr_name"]}</descr_name>
		<descr><![CDATA[{$langs[$lang]["startpaste-descr"]}]]></descr>
		<tipo>normal</tipo>
	</function>
	<function>
		<name>pasted</name>
		<privileged>0</privileged>
		<regex><![CDATA[{$langs[$lang]["pasted-regex"]}]]></regex>
		<descr_name>{$langs[$lang]["pasted-descr_name"]}</descr_name>
		<descr><![CDATA[{$langs[$lang]["pasted-descr"]}]]></descr>
		<tipo>normal</tipo>
	</function>
	<function>
		<name>pastetypes</name>
		<privileged>0</privileged>
		<regex><![CDATA[{$langs[$lang]["pastetypes-regex"]}]]></regex>
		<descr_name>{$langs[$lang]["pastetypes-descr_name"]}</descr_name>
		<descr><![CDATA[{$langs[$lang]["pastetypes-descr"]}]]></descr>
		<tipo>normal</tipo>
	</function>
	<function>
		<name>paste</name>
		<tipo>always</tipo>
	</function>
</db>

EOF;

		return $xml;
	}
?>