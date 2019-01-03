<?php

class Lang {

	static $lang;

	static function tryToSetLang($user_prefers, $cookie) { // I'll improve this method later
		if (!isset($cookie['lang'])) {
			$lang = substr($user_prefers, 0, 2);
			$acceptLang = ['ru', 'by', 'en', 'ua']; 
			self::$lang = in_array($lang, $acceptLang) ? $lang : 'ru';
			setcookie('lang', self::$lang, time() + 360000);
		} else {
			self::$lang = $cookie['lang'];
		}
	}

	// Function that returns string with specified $stringId from database with replaced values.
	// $whatToReplace should be an array like ['login' => 'Sample login']. This array will replace '%login%'
	// in text to 'Sample login'.
	static function getText($stringId, $whatToReplace = []) {
		require_once 'db.php';

		$translations = new DB(DB_HOST, DB_LOGIN, DB_PASSWORD, DB_NAME);
		$translations->setTable(DB_TABLE_PREFIX . 'language_' . self::$lang);

		$stringToReplace = $translations->getLines('text_pattern', "`pattern_key` = '{$stringId}'")[0]['text_pattern'];
		$keys = array_keys($whatToReplace);
		for ($i = 0; $i < count($keys); $i++) {
			$stringToReplace = str_replace('%' . $keys[$i] . '%', $whatToReplace[$keys[$i]], $stringToReplace);
		}

		$readyString = $stringToReplace;

		return $readyString;
	}

}