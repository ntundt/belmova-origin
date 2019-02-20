<?php

// header('Content-Type: text/json; charset=utf-8');

require_once __DIR__ . '/../config.php';

// $already_sent = false;

// if (0 < count($_POST)) {
// 	$parameters = $_POST;
// } else {
// 	$parameters = $_GET;
// }

// function makeResponse($response = [], $error = []) {
// 	global $already_sent;
// 	if (!$already_sent) {
// 		$return_object = [];
// 		if (0 === strcmp(gettype($response), 'array')) {
// 			if (0 < count(array_keys($response))) {
// 				$return_object['response'] = $response;
// 			}
// 		} else {
// 			$return_object['response'] = $response;
// 		}
// 		if (0 === strcmp(gettype($error), 'array')) {
// 			if (0 < count(array_keys($error))) {
// 				$return_object['error'] = $error;
// 			}
// 		} else {
// 			$return_object['error'] = $error;
// 		}
// 		echo json_encode($return_object, JSON_UNESCAPED_UNICODE);
// 		$already_sent = true;
// 	}
// }

// unset($parameters['uid']);

// if (isset($parameters['sid'])) {
// 	$parameters['uid'] = Auth::getUserId($parameters['sid']);
// 	if (false === $parameters['uid']) {
// 		ErrorList::addError(106);
// 		makeResponse([], ErrorList::makeAssoc());
// 	}
// 	unset($parameters['sid']);
// } else {
// 	ErrorList::addError(105);
// 	makeResponse([], ErrorList::makeAssoc());
// }


class ServerResponse {
	public $requestParameters;
	public $clientUserId;

	private $GETParameters;
	private $POSTParameters;
	private $cookies;
	private $requiredParameters;
	private $responseSent;

	/**
     * Define which of the request methods client uses depending on the array's size
     * @param $post
     * @param $get
     * @param $cookie
     * @return void
     */
	function __construct($post=[], $get=[], $cookie=[]) {
		unset(
			$get['uid'],
			$post['uid'],
			$cookie['uid']
		);
		if (0 < count($post)) {
			$this->requestParameters = $post;
		} else if (0 < count($get)) {
			$this->requestParameters = $get;
		} else if (0 < count($cookie)) {
			$this->requestParameters = $cookie;
		}
		$this->GETParameters = $get;
		$this->POSTParameters = $post;
		$this->cookies = $cookie;

		$this->responseSent = false;
		header('Content-Type: text/json; charset=utf-8');
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
					$this->exit();
				}
			}
		}
	}

	/**
     * Stop script executing and respond to the client that there's the fatal error during execution.
     * @return void
     */
	private function exit() {
		header("API-Errno: false");
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
			echo json_encode($return_object, JSON_UNESCAPED_UNICODE);
			$this->responseSent = true;
		}
	}

	/**
     * Check if the client authenticated. Exit with error if no.
     * @return int
     */
	function checkAuthentication() {
		if (isset($this->requestParameters['sid'])) {
			$this->clientUserId = Auth::getUserId($this->requestParameters['sid']);
			if (false === $this->clientUserId) {
				ErrorList::addError(106);
				$this->exit();
			}
		} else if (isset($this->cookies['sid'])) {
			$this->clientUserId = Auth::getUserId($this->cookies['sid']);
			if (false === $this->clientUserId) {
				ErrorList::addError(106);
				$this->exit();
			}
		} else {
			ErrorList::addError(105);
			$this->exit();
		}
		return $this->clientUserId;
	}
}