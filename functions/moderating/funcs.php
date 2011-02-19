<?php
	function verifica_badword($badword)
	{
		global $db;
		$result = $db->select(
			array("bad_words"),
			array("IDWord"),
			array(""),
			array("word"),
			array("="),
			array($badword)
		);

		if(count($result) == 0) {
			return $db->insert("bad_words", array("word", "count"), array($badword, 0));
		}

		return (int)$result[0]['IDWord'];
	}
?>