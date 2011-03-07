<?php
	require_once("../functions.php");
	require_once("../options.php");

	$_file = "./quotes.txt";

	touch($_file);

	/**
	 * Adds a quote
	 *
	 * @param int $id
	 * @param string $text
	 * @param string $pirla
	 * @param string $quoter
	 * @param string $chan
	 * @return string OK if added, NOK:MESSAGE if not
	 */
	function addQuote($id, $text, $pirla, $quoter, $chan)
	{
		global $_file;

		if(is_int($id) && word_count($pirla) == 1 && word_count($quoter) == 1 && word_count($chan) == 1 && !starts_with($chan, "#")) {
			if(!file_contains(file($_file), "$id|")) {
				$quote_line = $id . "|" . $text . "|" . $pirla . "|" . $quoter . "|#" . $chan . "\n";
				file_put_contents($_file, $quote_line, LOCK_EX | FILE_APPEND);
				if(file_contains(file($_file), $id)) {
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
	 * Removes a quote
	 *
	 * @param int $id
	 * @return string OK if added, NOK:MESSAGE if not
	 */
	function delQuote($id)
	{
		global $_file;

		if(is_int($id)) {
			$contents = file($_file);
			$pos = file_pos($contents, "$id|");
			if($pos >= 0) {
				unset($contents[$pos]);
				file_put_contents($_file, implode("\n", $contents), LOCK_EX);
				if(!file_contains(file($_file), $id)) {
					return "OK";
				} else {
					return "NOK:Error while removing";
				}
			} else {
				return "NOK:Quote not there";
			}
		} else {
			return "NOK:Wrong parameter";
		}
	}

	$pass = GET("psw");
	if($pass == $_password) {
		$action = GET("action");
		$id = (int)GET("id");
		if($action == "add") {
			$text = GET("quote");
			$pirla = GET("pirla");
			$quoter = GET("quotatore");
			$chan = GET("canale");
			echo addQuote($id, $text, $pirla, $quoter, $chan);
		} elseif($action == "del") {
			echo delQuote($id);
		} else {
			echo "NOK:Wrong action";
		}
	} else {
		echo "NOK:Wrong password";
	}
?>