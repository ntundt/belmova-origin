<?php

class Lang {

	static $lang;

	static function tryToSetLang($userPrefers, $cookie) { 
		if (!isset($cookie['lang'])) {
			$lang = substr($userPrefers, 0, 2);
			$accept_lang = ['ru', 'by', 'en', 'ua']; 
			self::$lang = in_array($lang, $accept_lang) ? $lang : 'ru';
			setcookie('lang', self::$lang, time() + 360000);
		} else {
			self::$lang = $cookie['lang'];
		}
	}

	static function getText($stringId, $whatToReplace = []) {
		Database::setCurrentTable('language_' . self::$lang);

		$stringToReplace = Database::getLines('text_pattern', "`pattern_key` = '{$stringId}'")[0]['text_pattern'];
		if (!isset($stringToReplace)) {
			Debug::addInfo("{$stringId} has no translation.");
		}
		$keys = array_keys($whatToReplace);
		for ($i = 0; $i < count($keys); $i++) {
			$stringToReplace = str_replace('%' . $keys[$i] . '%', $whatToReplace[$keys[$i]], $stringToReplace);
		}

		$readyString = $stringToReplace;

		Database::setPreviousTable();

		return $readyString;
	}

}
