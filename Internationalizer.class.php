<?php

	require_once("common.php");

	class Internationalizer
	{
		private $_translations;
		private $_lang;

		public function __construct($lang)
		{
			$this->_translations = array();
			$this->_lang = $lang;

			$this->getTranslations();
		}

		private function getTranslations()
		{
			$folders = getDirs("functions/");

			foreach($folders as $folder) {
				$basename = "functions/$folder/i18n/{$this->_lang}.xml";
				if(file_exists($basename)) {
					$xml = simplexml_load_file($basename);
					foreach($xml->translation as $translation)
						$this->_translations[(string)$translation->msgid] = (string)$translation->msgstr;
				}
			}

			print_r($this->_translations);
		}

		public function bot_gettext($key)
		{
			if(array_key_exists($key, $this->_translations))
				return $this->_translations[$key];

			return $key;
		}
	}

?>