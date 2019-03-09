<?php

require_once __DIR__ . '/../config.php';

class ServerResponse {
	public $requestParameters = [];
	public $clientUserId;

	private $GETParameters;
	private $POSTParameters;
	private $cookies;
	private $requiredParameters = [];
	private $responseSent;

	/**
	 * Define which of the request methods client uses depending on the array's size
	 * @param $post
	 * @param $get
	 * @param $cookie
	 * @return void
	 */
	function __construct($post=[], $get=[], $cookie=[]) {
		if (0 < count($post)) {
			$this->requestParameters = array_merge($this->requestParameters, $post);
		}
		if (0 < count($get)) {
			$this->requestParameters = array_merge($this->requestParameters, $get);
		}
		if (0 < count($cookie)) {
			$this->requestParameters = array_merge($this->requestParameters, $cookie);
		}
		$this->GETParameters = $get;
		$this->POSTParameters = $post;
		$this->cookies = $cookie;
		unset($this->requestParameters['uid']);
		$this->responseSent = false;
	}

	/**
	 * Set the parameters which must be in the request to make it possible to process it.
	 * @param $required
	 * @return void
	 */
	function setRequiredParameters($required) {
		$this->requiredParameters = $required;
	}

	/**
	 * Check if all the required parameters are set. Exit with error if no.
	 * @return void
	 */
	function checkParameters() {
		if (0 < count($this->requiredParameters)) {
			for ($i = 0; $i < count($this->requiredParameters); $i++) {
				if (!isset($this->requestParameters[$this->requiredParameters[$i]])) {
					ErrorList::addError(108);
					$this->exit(108);
				}
			}
		}
	}

	/**
	 * Stop script executing and respond to the client that there's the fatal error during execution.
	 * @return void
	 */
	private function exit($code) {
		http_response_code(400);
		header('API-Error-Code: ' . $code);
		header('API-Errno: 0');
		header('API-No-Response: 1');
		$this->makeResponse();
		exit();
	}

	/**
	 * Echo response as JSON object.
	 * @return void
	 */
	public function makeResponse($response=[], $error=[]) {
		if (0 === count($error)) {
			$error = ErrorList::makeAssoc();
		}
		if (!$this->responseSent) {
			header('Content-Type: text/json; charset=utf-8');
			$return_object = [];
			if (0 === strcmp(gettype($response), 'array')) {
				if (0 < count($response)) {
					$return_object['response'] = $response;
				}
			} else {
				$return_object['response'] = $response;
			}
			if (0 === strcmp(gettype($error), 'array')) {
				if (0 < count($error)) {
					$return_object['error'] = $error;
				}
			} else {
				$return_object['error'] = $error;
			}
			if (DEBUG) {
				$return_object['debug_info'] = Debug::$info;
			}
			if (isset($return_object['error'])) {
				http_response_code(400);
				header('API-Errno: 0');
				header('API-Error-Code: ' . $error['error_code']);
				if (isset($return_object['response'])) {
					header('API-No-Response: 0');
				} else {
					header('API-No-Response: 1');
				}
			} else {
				header('API-Errno: 1');
				header('API-No-Response: 0');
			}
			echo json_encode($return_object, JSON_UNESCAPED_UNICODE);
			$this->responseSent = true;
		}
	}

	/**
	 * Check if the client authenticated. Exit with error if no.
	 * @return int
	 */
	function checkAuthentication($doNotExit=false) {
		$this->clientUserId = false;
		if (isset($this->requestParameters['sid'])) {
			$this->clientUserId = Auth::getUserId($this->requestParameters['sid']);
			if (false === $this->clientUserId and !$doNotExit) {
				ErrorList::addError(106);
				$this->exit();
			}
		} else if (isset($this->cookies['sid'])) {
			$this->clientUserId = Auth::getUserId($this->cookies['sid']);
			if (false === $this->clientUserId and !$doNotExit) {
				ErrorList::addError(106);
				$this->exit();
			}
		} else if (!$doNotExit) {
			ErrorList::addError(105);
			$this->exit();
		}
		return $this->clientUserId;
	}
}
