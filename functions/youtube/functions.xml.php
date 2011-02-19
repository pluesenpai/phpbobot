<?php
	function youtube_generateXml($lang)
	{
		$langs["it_IT"] = array(
			"youtube-regex" => "/^(youtube|yt) (.+?)$/",
			"youtube-descr_name" => "youtube|yt {CODICE}|{URL}",
			"youtube-descr" => "Scriver&ograve; in canale il titolo e il link del video."
		);

		$langs["en_GB"] = array(
			"youtube-regex" => "/^(youtube|yt) (.+?)$/",
			"youtube-descr_name" => "youtube|yt {CODE}|{URL}",
			"youtube-descr" => "I'll post in chan the title and the link of the video."
		);
		
		$xml = <<<EOF
<db>
	<function>
		<name>youtube</name>
		<privileged>0</privileged>
		<regex><![CDATA[{$langs[$lang]["youtube-regex"]}]]></regex>
		<descr_name>{$langs[$lang]["youtube-descr_name"]}</descr_name>
		<descr><![CDATA[{$langs[$lang]["youtube-descr"]}]]></descr>
		<tipo>normal</tipo>
	</function>
	<function>
		<name>youtube</name>
		<tipo>always</tipo>
	</function>
</db>

EOF;

		return $xml;
	}
?>