<?php

class OutputError {

	public $code;
	public $errno;

	function __construct($code) {
		$this->code = $code;
		$this->errno = false;
	}

	function getDescription() {
		return Lang::getText('error_' . $this->code);
	}

	function makeAssoc() {
		require_once 'lang.php';
		return ['error_code' => $this->code, 'error_description' => $this->getDescription()];
	}

}

class ErrorList {

	private static $error_list = [];

	private static function isThereErrorWithCode($code) {
		for ($i = 0; $i < count(self::$error_list); $i++) {
			if (self::$error_list[$i]->code === $code) {
				return true;
			}
		}
		return false;
	}

	public static function addError($error) {
		if (0 === strcmp(gettype($error), 'object')) {
			if (!self::isThereErrorWithCode($error->code)) { 
				self::$error_list[] = $error;
			}
		} else {
			if (!self::isThereErrorWithCode($error)) { 
				self::$error_list[] = new OutputError($error);
			}
		}
	}

	public static function makeAssoc() {
		$error_array = [];
		if (count(self::$error_list) > 1) {
			for ($i = 0; $i < count(self::$error_list); $i++) {
				$error_array[] = self::$error_list[$i]->makeAssoc();
			}
		} else if (count(self::$error_list) == 1) {
			$error_array = self::$error_list[0]->makeAssoc();
		} 
		return $error_array;
	}

}
