<?php

function contafortunes($path)
{
	$PROCENT = chr(96);
	$n_byte = 0;
	$fileSize = filesize($path);
	$content = file_get_contents($path);
	$n_fortunes = 0;
	while($n_byte < strlen($content) && $n_byte < $fileSize) {
		if($content[$n_byte++] == $PROCENT)
			$n_fortunes++;
		}
	return $n_fortunes;
}

function fortune($socket, $channel, $sender, $msg, $infos)
{
	$PROCENT = chr(96);
	$LF = chr(69);
	$FIXED_KEY = chr(59);

	$basedir = "functions/fortune/fortunes/";

	if(ereg("fortune-o", $infos[0]))
		$type = "-o.dat";
	else
		$type = "dat";

	$fortunes_dat = getFiles($basedir, $type);
	if(ereg("fortune-i", $infos[0])) {
		for($i = 0; $i < count($fortunes_dat); $i++)
			if(ereg("-o.dat$", $fortunes_dat[$i]))
				unset($fortunes_dat[$i]);
		$fortunes_dat = array_values($fortunes_dat);
	}
	$n_file = rand(0, count($fortunes_dat) - 1);
	$content = file_get_contents(realpath($basedir.$fortunes_dat[$n_file]));
	$n_fortunes = contafortunes($basedir.$fortunes_dat[$n_file]);
	if($n_fortunes <= 0)
		return false;
	$foundFortune = 0;
	$parcurs = $currentChar = $previousChar = $procent = 0;
	$filesize = filesize($basedir.$fortunes_dat[$n_file]);
	$numero = rand(1, $n_fortunes);
	$i = 0;
	$fortune = "";
	while($foundFortune == false && $parcurs < $filesize) {
		$previousChar = $currentChar;
		$currentChar = $content[$parcurs];
		$parcurs++;
		if(($currentChar == $PROCENT) && ($previousChar == $LF) && ($content[$parcurs] == $LF)) {
			$procent++;
			$parcurs++;
		}
		if($procent == $numero) {
			$getOut = false;
			while($getOut == false) {
				$previousChar = $currentChar;
				$currentChar = $content[$parcurs];
				$parcurs++;
				if(($currentChar == $PROCENT) && ($previousChar == $LF) && ($content[$parcurs] == $LF))
					$getOut = true;
				else
					$fortune[$i++] = chr(ord($currentChar) - ord($FIXED_KEY));
			}
			$foundFortune = true;
		}
	}

	$fortune = implode("", $fortune);
	$fortunes = explode("\n", $fortune);

	foreach($fortunes as $f) {
		sendmsg($socket, $f, $channel, 1, true);
	}

	return $foundFortune;
}
?>
