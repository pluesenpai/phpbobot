<?php
	function builtins_generateXml($lang)
	{
		$array = array(
			"greet" => "0",
			"greetme" => "0",
			"greetuser" => "0",
			"silence" => "1",
			"silenceall" => "1",
			"talk" => "1",
			"talkall" => "1",
			"help" => "0",
			//"shorthelp" => "0",
			"grouphelp" => "0",
			"botversion" => "0",
			"register" => "0",
			"auth" => "0",
			"deauth" => "0",
			"lists_add" => "1",
			"lists_remove" => "1",
			"setmessage" => "0",
			"delmessage" => "0"
		);

		$xml = "<db>\n";

		foreach($array as $item => $priv) {
			$xml .= "\t<function>\n";
			$xml .= "\t\t<name>{$item}</name>\n";
			$xml .= "\t\t<privileged>{$priv}</privileged>\n";
			$xml .= "\t\t<regex><![CDATA[" . _("{$item}-regex") ."]]></regex>\n";
			$xml .= "\t\t<descr_name><![CDATA[" . _("{$item}-descr_name") ."]]></descr_name>\n";
			$xml .= "\t\t<descr><![CDATA[" . _("{$item}-descr") ."]]></descr>\n";
			$xml .= "\t\t<tipo>normal</tipo>\n";
			$xml .= "\t</function>\n";
		}

		$xml .= "</db>\n";

		return $xml;
	}
?>