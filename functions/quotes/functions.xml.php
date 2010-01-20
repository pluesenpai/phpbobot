<?php
	function quotes_generateXml($lang)
	{
		$langs["it_IT"] = array(
			"addquote-regex" => "/^quota (.+?) (.+)$/",
			"addquote-descr_name" => "quota {TEXT}",
			"addquote-descr" => "Aggiungerai una citazione",
			//-------------------------------------------
			"quote-regex" => "/^quote ([0-9]+)$/",
			"quote-descr_name" => "quote {NUMERO}",
			"quote-descr" => "Mostro la citazione numero {NUMERO}",
			//-------------------------------------------
			"randquote-regex" => "/^randquote$/",
			"randquote-descr_name" => "randquote",
			"randquote-descr" => "Mostro una citazione casuale",
			//-------------------------------------------
			"delquote-regex" => "/^delquote ([0-9]+)$/",
			"delquote-descr_name" => "delquote {NUMERO}",
			"delquote-descr" => "Elimino la quote numero {NUMERO}",
			//-------------------------------------------
			"userquotes-regex" => "/^quoteutente (.+)$/",
			"userquotes-descr_name" => "quoteutente {NOMI}",
			"userquotes-descr" => "Mosto le ultime 5 citazioni dell'utente/degli utenti {NOMI}",
			//-------------------------------------------
			"findquotes-regex" => "/^trovaquotes (.+)$/",
			"findquotes-descr_name" => "trovaquotes {PAROLE}",
			"findquotes-descr" => "Mostro le ultime 5 citazioni contenenti {PAROLE}",
			//-------------------------------------------
			"allquotes-regex" => "/^tutte/",
			"allquotes-descr_name" => "tutte",
			"allquotes-descr" => "Mostro tutte le citazioni (in privato)"
		);

		$langs["en_GB"] = array(
			"addquote-regex" => "/^addquote (.+?) (.+)$/",
			"addquote-descr_name" => "addquote {QUOTE_TEXT}",
			"addquote-descr" => "You will add a quote",
			//-------------------------------------------
			"quote-regex" => "/^quote ([0-9]+)$/",
			"quote-descr_name" => "quote {NUMBER}",
			"quote-descr" => "You will get the text of the quote number {NUMBER}",
			//-------------------------------------------
			"randquote-regex" => "/^randquote$/",
			"randquote-descr_name" => "randquote",
			"randquote-descr" => "You will get a random quote",
			//-------------------------------------------
			"delquote-regex" => "/^delquote ([0-9]+)$/",
			"delquote-descr_name" => "delquote {NUMBER}",
			"delquote-descr" => "You will drop the quote number {NUMBER}",
			//-------------------------------------------
			"userquotes-regex" => "/^userquotes (.+)$/",
			"userquotes-descr_name" => "userquotes {MULTIPLE_USER_NAME}",
			"userquotes-descr" => "You will get the last 5 quotes of {MULTIPLE_USER_NAME}",
			//-------------------------------------------
			"findquotes-regex" => "/^findquotes (.+)$/",
			"findquotes-descr_name" => "findquotes {MULTIPLE_QUERIES}",
			"findquotes-descr" => "You will get the last 5 quotes which contains {MULTIPLE_QUERIES}",
			//-------------------------------------------
			"allquotes-regex" => "/^allquotes$/",
			"allquotes-descr_name" => "allquotes",
			"allquotes-descr" => "You will get all quotes (in private)"
		);
		
		$xml = <<<EOF
<db>
	<function>
		<name>addquote</name>
		<privileged>0</privileged>
		<regex><![CDATA[{$langs[$lang]["addquote-regex"]}]]></regex>
		<descr_name>{$langs[$lang]["addquote-descr_name"]}</descr_name>
		<descr><![CDATA[{$langs[$lang]["addquote-descr"]}]]></descr>
		<tipo>normal</tipo>
	</function>
	<function>
		<name>quote</name>
		<privileged>0</privileged>
		<regex><![CDATA[{$langs[$lang]["quote-regex"]}]]></regex>
		<descr_name>{$langs[$lang]["quote-descr_name"]}</descr_name>
		<descr><![CDATA[{$langs[$lang]["quote-descr"]}]]></descr>
		<tipo>normal</tipo>
	</function>
	<function>
		<name>randquote</name>
		<privileged>0</privileged>
		<regex><![CDATA[{$langs[$lang]["randquote-regex"]}]]></regex>
		<descr_name>{$langs[$lang]["randquote-descr_name"]}</descr_name>
		<descr><![CDATA[{$langs[$lang]["randquote-descr"]}]]></descr>
		<tipo>normal</tipo>
	</function>
	<function>
		<name>delquote</name>
		<privileged>1</privileged>
		<regex><![CDATA[{$langs[$lang]["delquote-regex"]}]]></regex>
		<descr_name>{$langs[$lang]["delquote-descr_name"]}</descr_name>
		<descr><![CDATA[{$langs[$lang]["delquote-descr"]}]]></descr>
		<tipo>normal</tipo>
	</function>
	<function>
		<name>userquotes</name>
		<privileged>0</privileged>
		<regex><![CDATA[{$langs[$lang]["userquotes-regex"]}]]></regex>
		<descr_name>{$langs[$lang]["userquotes-descr_name"]}</descr_name>
		<descr><![CDATA[{$langs[$lang]["userquotes-descr"]}]]></descr>
		<tipo>normal</tipo>
	</function>
	<function>
		<name>findquotes</name>
		<privileged>0</privileged>
		<regex><![CDATA[{$langs[$lang]["findquotes-regex"]}]]></regex>
		<descr_name>{$langs[$lang]["findquotes-descr_name"]}</descr_name>
		<descr><![CDATA[{$langs[$lang]["findquotes-descr"]}]]></descr>
		<tipo>normal</tipo>
	</function>
	<function>
		<name>allquotes</name>
		<privileged>0</privileged>
		<regex><![CDATA[{$langs[$lang]["allquotes-regex"]}]]></regex>
		<descr_name>{$langs[$lang]["allquotes-descr_name"]}</descr_name>
		<descr><![CDATA[{$langs[$lang]["allquotes-descr"]}]]></descr>
		<tipo>normal</tipo>
	</function>
</db>

EOF;

		return $xml;
	}
?>