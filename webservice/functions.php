<?php

	/**
	 * Checks if file contains a line that starts with $search
	 *
	 * @param string $file
	 * @param string $search String to search
	 * @param string $direction If > 0 from beginning to end, else from end to beginning of file
	 * @return boolean true if found, false otherwise
	 */
	function file_contains($file, $search, $direction = 1)
	{
		return (file_pos($file, $search, $direction) >= 0);
	}

	/**
	 * Returns in which position of the file is the line that starts with $search
	 *
	 * @param string $file
	 * @param string $search String to search
	 * @param string $direction If > 0 from beginning to end, else from end to beginning of file
	 * @return int position if found or -1
	 */
	function file_pos($file, $search, $direction = 1)
	{
		$rowCount = count($file);
		if($direction > 0) {
			for($i = 0; $i < $rowCount; $i++) {
				if(starts_with($file[$i], "$search")) {
					return $i;
				}
			}
		} else {
			for($i = $rowCount; $i > 0; $i--) {
				if(starts_with($file[$i], "$search")) {
					return $i;
				}
			}
		}

		return -1;
	}

	/**
	 * Safe access to array $_GET
	 *
	 * @param string $key The key to search in the array
	 * @return string Empty string if key is not found or escaped value
	 */
	function GET($key)
	{
		return isset($_GET[$key]) ? str_esc($_GET[$key]) : "";
	}

	/**
	 * Check if given string starts with $search
	 *
	 * @param string $string The string to check
	 * @param string $search The string to search
	 * @return boolean True if starts, false otherwise
	 */
	function starts_with($string, $search)
	{
		return substr($string, 0, strlen($search)) == "{$search}";
	}

	/**
	 * Escapes quotes from strings
	 *
	 * @param string $stringa The string to escape
	 * @return string The escaped string
	 */
	function str_esc($stringa)
	{
		if(get_magic_quotes_gpc())
			return $stringa;
		else
			return addslashes($stringa);
	}

	/**
	 * Counts how many words are in the given string
	 * @param string $stringa The string
	 * @return int How many words are in given string
	 */
	function word_count($stringa)
	{
		return count(explode(" ", $stringa));
	}

?>