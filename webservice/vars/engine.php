<?php
	require_once("../functions.php");
	require_once("../options.php");

	$_file = "./vars.txt";

	touch($_file);

	/**
	 * Adds a variable
	 *
	 * @param string $var
	 * @param string $meaning
	 * @return string OK if added, NOK:MESSAGE if not
	 */
	function addVar($var, $meaning)
	{
		global $_file;

		if(word_count($var) == 1) {
			if(!file_contains(file($_file), "$var|")) {
				$var_line = $var . "|" . $meaning . "\n";
				file_put_contents($_file, $var_line, LOCK_EX | FILE_APPEND);
				if(file_contains(file($_file), $var)) {
					return "OK";
				} else {
					return "NOK:Error while inserting";
				}
			} else {
				return "NOK:Already there";
			}
		} else {
			return "NOK:Wrong parameter";
		}
	}

	/**
	 * Removes a var
	 *
	 * @param string $var
	 * @return string OK if added, NOK:MESSAGE if not
	 */
	function delVar($var)
	{
		global $_file;

		if(word_count($var) == 1) {
			$contents = file($_file);
			$pos = file_pos($contents, "$var|");
			if($pos >= 0) {
				unset($contents[$pos]);
				file_put_contents($_file, implode("\n", $contents), LOCK_EX);
				if(!file_contains(file($_file), $var)) {
					return "OK";
				} else {
					return "NOK:Error while removing";
				}
			} else {
				return "NOK:Var not there";
			}
		} else {
			return "NOK:Wrong parameter";
		}
	}

	$pass = GET("psw");
	if($pass == $_password) {
		$action = GET("action");
		$var = GET("var");
		if($action == "add") {
			$meaning = GET("meaning");
			echo addVar($var, $meaning);
		} elseif($action == "del") {
			echo delVar($var);
		} else {
			echo "NOK:Wrong action";
		}
	} else {
		echo "NOK:Wrong password";
	}
?>