<?php

	require_once("functions.xml.php");
	$var = builtins_generateXml("it_IT");
	preg_match_all("/\<!\[CDATA\[(.+)\]\]\>/", $var, $ret);

	echo "<?php\n";
	foreach($ret[1] as $item) {
		echo "\techo _(\"{$item}\");\n";
	}
	echo "?>\n";
?>
