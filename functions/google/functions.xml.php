<?php
	function google_generateXml($lang)
	{
		$langs["it_IT"] = array(
			"google-regex" => "/^(g|google) (.+?)$/",
			"google-descr_name" => "google {KEYWORDS}",
			"google-descr" => "Userò google per cercare {KEYWORDS}"
		);

		$langs["en_GB"] = array(
			"google-regex" => "/^(g|google) (.+?)$/",
			"google-descr_name" => "google {KEYWORDS}",
			"google-descr" => "I'll use Google to search {KEYWORDS}."
		);
		
		$xml = <<<EOF
<db>
	<function>
		<name>google</name>
		<privileged>0</privileged>
		<regex><![CDATA[{$langs[$lang]["google-regex"]}]]></regex>
		<descr_name>{$langs[$lang]["google-descr_name"]}</descr_name>
		<descr><![CDATA[{$langs[$lang]["google-descr"]}]]></descr>
		<tipo>normal</tipo>
	</function>
</db>

EOF;

		return $xml;
	}
?>