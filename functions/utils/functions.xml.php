<?php
	function utils_generateXml($lang)
	{
		$langs["it_IT"] = array(
			"datetime-regex" => "/^dataora$/i",
			"datetime-descr_name" => "dataora",
			"datetime-descr" => "Mostro la data e l'ora correnti (UTC)",
			//------------------------------------------
			"date-regex" => "/^data$/",
			"date-descr_name" => "data",
			"date-descr" => "Mostro la data corrente",
			//------------------------------------------
			"time-regex" => "/^ora$/",
			"time-descr_name" => "ora",
			"time-descr" => "Mostro l'ora corrente (UTC)",
			//------------------------------------------
			"op-regex" => "/^op (.+)$/",
			"op-descr_name" => "op {UTENTE}",
			"op-descr" => "Assegno lo stato di op a {UTENTE}",
			//------------------------------------------
			"deop-regex" => "/^deop (.+)$/",
			"deop-descr_name" => "deop {UTENTE}",
			"deop-descr" => "Tolgo lo stato di op a {UTENTE}",
			//------------------------------------------
			"halfop-regex" => "/^halfop (.+)$/",
			"halfop-descr_name" => "halfop {UTENTE}",
			"halfop-descr" => "Assegno lo stato di hop a {UTENTE}",
			//------------------------------------------
			"dehalfop-regex" => "/^dehalfop (.+)$/",
			"dehalfop-descr_name" => "dehalfop {UTENTE}",
			"dehalfop-descr" => "Tolgo lo stato di hop a {UTENTE}",
			//------------------------------------------
			"voice-regex" => "/^voice (.+)$/",
			"voice-descr_name" => "voice {UTENTE}",
			"voice-descr" => "Assegno lo stato di voice a {UTENTE}",
			//------------------------------------------
			"devoice-regex" => "/^devoice (.+)$/",
			"devoice-descr_name" => "devoice {UTENTE}",
			"devoice-descr" => "Tolgo lo stato di voice a {UTENTE}"
		);

		$langs["en_GB"] = array(
			"datetime-regex" => "/^datetime$/i",
			"datetime-descr_name" => "datetime",
			"datetime-descr" => "You will get current date and time (UTC)",
			//------------------------------------------
			"date-regex" => "/^date$/",
			"date-descr_name" => "date",
			"date-descr" => "You will get current date",
			//------------------------------------------
			"time-regex" => "/^time$/",
			"time-descr_name" => "time",
			"time-descr" => "You will get current time (UTC)",
			//------------------------------------------
			"op-regex" => "/^op (.+)$/",
			"op-descr_name" => "op {USER}",
			"op-descr" => "I'll op {USER}",
			//------------------------------------------
			"deop-regex" => "/^deop (.+)$/",
			"deop-descr_name" => "deop {USER}",
			"deop-descr" => "I'll deop {USER}",
			//------------------------------------------
			"halfop-regex" => "/^halfop (.+)$/",
			"halfop-descr_name" => "halfop {USER}",
			"halfop-descr" => "I'll hop {USER}",
			//------------------------------------------
			"dehalfop-regex" => "/^dehalfop (.+)$/",
			"dehalfop-descr_name" => "dehalfop {USER}",
			"dehalfop-descr" => "I'll dehop {USER}",
			//------------------------------------------
			"voice-regex" => "/^voice (.+)$/",
			"voice-descr_name" => "voice {USER}",
			"voice-descr" => "I'll give voice to {USER}",
			//------------------------------------------
			"devoice-regex" => "/^devoice (.+)$/",
			"devoice-descr_name" => "devoice {USER}",
			"devoice-descr" => "I'll remove voice from {USER}"
		);
		
		$xml = <<<EOF
<db>
	<function>
		<name>datetime</name>
		<privileged>0</privileged>
		<regex><![CDATA[{$langs[$lang]["datetime-regex"]}]]></regex>
		<descr_name>{$langs[$lang]["datetime-descr_name"]}</descr_name>
		<descr><![CDATA[{$langs[$lang]["datetime-descr"]}]]></descr>
		<tipo>normal</tipo>
	</function>
	<function>
		<name>date</name>
		<privileged>0</privileged>
		<regex><![CDATA[{$langs[$lang]["date-regex"]}]]></regex>
		<descr_name>{$langs[$lang]["date-descr_name"]}</descr_name>
		<descr><![CDATA[{$langs[$lang]["date-descr"]}]]></descr>
		<tipo>normal</tipo>
	</function>
	<function>
		<name>time</name>
		<privileged>0</privileged>
		<regex><![CDATA[{$langs[$lang]["time-regex"]}]]></regex>
		<descr_name>{$langs[$lang]["time-descr_name"]}</descr_name>
		<descr><![CDATA[{$langs[$lang]["time-descr"]}]]></descr>
		<tipo>normal</tipo>
	</function>
	<function>
		<name>modes</name>
		<privileged>1</privileged>
		<regex><![CDATA[{$langs[$lang]["op-regex"]}]]></regex>
		<descr_name>{$langs[$lang]["op-descr_name"]}</descr_name>
		<descr><![CDATA[{$langs[$lang]["op-descr"]}]]></descr>
		<tipo>normal</tipo>
	</function>
	<function>
		<name>modes</name>
		<privileged>1</privileged>
		<regex><![CDATA[{$langs[$lang]["deop-regex"]}]]></regex>
		<descr_name>{$langs[$lang]["deop-descr_name"]}</descr_name>
		<descr><![CDATA[{$langs[$lang]["deop-descr"]}]]></descr>
		<tipo>normal</tipo>
	</function>
	<function>
		<name>modes</name>
		<privileged>1</privileged>
		<regex><![CDATA[{$langs[$lang]["halfop-regex"]}]]></regex>
		<descr_name>{$langs[$lang]["halfop-descr_name"]}</descr_name>
		<descr><![CDATA[{$langs[$lang]["halfop-descr"]}]]></descr>
		<tipo>normal</tipo>
	</function>
	<function>
		<name>modes</name>
		<privileged>1</privileged>
		<regex><![CDATA[{$langs[$lang]["dehalfop-regex"]}]]></regex>
		<descr_name>{$langs[$lang]["dehalfop-descr_name"]}</descr_name>
		<descr><![CDATA[{$langs[$lang]["dehalfop-descr"]}]]></descr>
		<tipo>normal</tipo>
	</function>
	<function>
		<name>modes</name>
		<privileged>1</privileged>
		<regex><![CDATA[{$langs[$lang]["voice-regex"]}]]></regex>
		<descr_name>{$langs[$lang]["voice-descr_name"]}</descr_name>
		<descr><![CDATA[{$langs[$lang]["voice-descr"]}]]></descr>
		<tipo>normal</tipo>
	</function>
	<function>
		<name>modes</name>
		<privileged>1</privileged>
		<regex><![CDATA[{$langs[$lang]["devoice-regex"]}]]></regex>
		<descr_name>{$langs[$lang]["devoice-descr_name"]}</descr_name>
		<descr><![CDATA[{$langs[$lang]["devoice-descr"]}]]></descr>
		<tipo>normal</tipo>
	</function>
</db>

EOF;

		return $xml;
	}
?>