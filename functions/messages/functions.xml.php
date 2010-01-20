<?php
	function messages_generateXml($lang)
	{
		$langs["it_IT"] = array(
			"messages-regex" => "/^messaggi$/",
			"messages-descr_name" => "messaggi",
			"messages-descr" => "Potrai leggere i messaggi non letti.",
			//--------------------------------------------------------------
			"allmessages-regex" => "/^messaggi\+$/",
			"allmessages-descr_name" => "messaggi+",
			"allmessages-descr" => "Potrai leggere tutti i tuoi messaggi.",
			//--------------------------------------------------------------
			"messages_from-regex" => "/^messaggi da \b(.+)\b$/",
			"messages_from-descr_name" => "messaggi da {USER}",
			"messages_from-descr" => "Potrai leggere i messaggi non letti da {UTENTE}",
			//--------------------------------------------------------------
			"allmessages_from-regex" => "/^messaggi\+ da \b(.+)\b$/",
			"allmessages_from-descr_name" => "messaggi+ da {UTENTE}",
			"allmessages_from-descr" => "Potrai leggere tutti i messaggi da {UTENTE}",
			//--------------------------------------------------------------
			"read-regex" => "/^leggi ([0-9]+)$/",
			"read-descr_name" => "leggi {NUMERO}",
			"read-descr" => "Potrai leggere il messaggio non letto numero {NUMERO}",
			//--------------------------------------------------------------
			"readall-regex" => "/^leggi\+ ([0-9]+)$/",
			"readall-descr_name" => "leggi+ {NUMERO}",
			"readall-descr" => "Potrai leggere il messaggio numero {NUMERO}",
			//--------------------------------------------------------------
			"message-regex" => "/^messaggio (.+?) (.+)$/",
			"message-descr_name" => "messaggio {UTENTE} {MESSAGGIO}",
			"message-descr" => "Invierai il tuo messaggio {MESSAGGIO} a {UTENTE}",
			//--------------------------------------------------------------
			"removemsg-regex" => "/^eliminamsg ([0-9]+)$/",
			"removemsg-descr_name" => "eliminamsg {NUMERO}",
			"removemsg-descr" => "Eliminerai il messaggio ricevuto numero {NUMERO}"
		);

		$langs["en_GB"] = array(
			"messages-regex" => "/^messages$/",
			"messages-descr_name" => "messages",
			"messages-descr" => "You will get your unread messages",
			//--------------------------------------------------------------
			"allmessages-regex" => "/^allmessages$/",
			"allmessages-descr_name" => "allmessages",
			"allmessages-descr" => "You will get all your messages",
			//--------------------------------------------------------------
			"messages_from-regex" => "/^messages from \b(.+)\b$/",
			"messages_from-descr_name" => "messages from {USER}",
			"messages_from-descr" => "You will get your unread messages from {USER}",
			//--------------------------------------------------------------
			"allmessages_from-regex" => "/^allmessages from \b(.+)\b$/",
			"allmessages_from-descr_name" => "allmessages from {USER}",
			"allmessages_from-descr" => "You will get all your messages from {USER}",
			//--------------------------------------------------------------
			"read-regex" => "/^read ([0-9]+)$/",
			"read-descr_name" => "read {NUMBER}",
			"read-descr" => "You will read your unread message number {NUMBER}",
			//--------------------------------------------------------------
			"readall-regex" => "/^readall ([0-9]+)$/",
			"readall-descr_name" => "readall {NUMBER}",
			"readall-descr" => "You will read your message number {NUMBER}",
			//--------------------------------------------------------------
			"message-regex" => "/^message (.+?) (.+)$/",
			"message-descr_name" => "message {USER} {MESSAGE}",
			"message-descr" => "You will send your message {MESSAGE} to {USER}",
			//--------------------------------------------------------------
			"removemsg-regex" => "/^removemsg ([0-9]+)$/",
			"removemsg-descr_name" => "removemsg {NUMBER}",
			"removemsg-descr" => "You will delete your received message number {NUMBER}"
		);
		
		$xml = <<<EOF
<db>
	<function>
		<name>getmsgs</name>
		<privileged>0</privileged>
		<regex><![CDATA[{$langs[$lang]["messages-regex"]}]]></regex>
		<descr_name>{$langs[$lang]["messages-descr_name"]}</descr_name>
		<descr><![CDATA[{$langs[$lang]["messages-descr"]}]]></descr>
		<tipo>normal</tipo>
	</function>
	<function>
		<name>getmsgs</name>
		<privileged>0</privileged>
		<regex><![CDATA[{$langs[$lang]["allmessages-regex"]}]]></regex>
		<descr_name>{$langs[$lang]["allmessages-descr_name"]}</descr_name>
		<descr><![CDATA[{$langs[$lang]["allmessages-descr"]}]]></descr>
		<tipo>normal</tipo>
	</function>
	<function>
		<name>getmsgs</name>
		<privileged>0</privileged>
		<regex><![CDATA[{$langs[$lang]["messages_from-regex"]}]]></regex>
		<descr_name>{$langs[$lang]["messages_from-descr_name"]}</descr_name>
		<descr><![CDATA[{$langs[$lang]["messages_from-descr"]}]]></descr>
		<tipo>normal</tipo>
	</function>
	<function>
		<name>getmsgs</name>
		<privileged>0</privileged>
		<regex><![CDATA[{$langs[$lang]["allmessages_from-regex"]}]]></regex>
		<descr_name>{$langs[$lang]["allmessages_from-descr_name"]}</descr_name>
		<descr><![CDATA[{$langs[$lang]["allmessages_from-descr"]}]]></descr>
		<tipo>normal</tipo>
	</function>
	<function>
		<name>getmsgs</name>
		<privileged>0</privileged>
		<regex><![CDATA[{$langs[$lang]["read-regex"]}]]></regex>
		<descr_name>{$langs[$lang]["read-descr_name"]}</descr_name>
		<descr><![CDATA[{$langs[$lang]["read-descr"]}]]></descr>
		<tipo>normal</tipo>
	</function>
	<function>
		<name>getmsgs</name>
		<privileged>0</privileged>
		<regex><![CDATA[{$langs[$lang]["readall-regex"]}]]></regex>
		<descr_name>{$langs[$lang]["readall-descr_name"]}</descr_name>
		<descr><![CDATA[{$langs[$lang]["readall-descr"]}]]></descr>
		<tipo>normal</tipo>
	</function>
	<function>
		<name>msgsend</name>
		<privileged>0</privileged>
		<regex><![CDATA[{$langs[$lang]["message-regex"]}]]></regex>
		<descr_name>{$langs[$lang]["message-descr_name"]}</descr_name>
		<descr><![CDATA[{$langs[$lang]["message-descr"]}]]></descr>>
		<tipo>normal</tipo>
	</function>
	<function>
		<name>removemsg</name>
		<privileged>0</privileged>
		<regex><![CDATA[{$langs[$lang]["removemsg-regex"]}]]></regex>
		<descr_name>{$langs[$lang]["removemsg-descr_name"]}</descr_name>
		<descr><![CDATA[{$langs[$lang]["removemsg-descr"]}]]></descr>
		<tipo>normal</tipo>
	</function>
	<function>
		<name>getmsgs</name>
		<tipo>join</tipo>
	</function>
</db>

EOF;

		return $xml;
	}
?>