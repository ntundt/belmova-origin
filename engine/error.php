<?php

class OutputError {

	public $code;
	public $errno;

	function __construct($code) {
		$this->code = $code;
		$this->errno = false;
	}

	function getText() {
		return Lang::getText('error_' . $this->code);
	}

	function makeAssoc() {
		require_once 'lang.php';
		return ['errno' => $this->errno, 'error_code' => $this->code, 'error_description' => $this->getText()];
	}

}
