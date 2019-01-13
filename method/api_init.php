<?php

header('Content-Type: text/json; charset=utf-8');

require_once __DIR__ . '/../config.php';

if (count($_POST) > 0) {
	$parameters = $_POST;
} else {
	$parameters = $_GET;
}

function makeResponse($response = [], $error = []) {
	$return_object = [];
	if (0 === strcmp(gettype($response), 'array')) {
		if (count(array_keys($response)) > 0) {
			$return_object['response'] = $response;
		}
	} else {
		$return_object['response'] = $response;
	}
	if (0 === strcmp(gettype($error), 'array')) {
		if (count(array_keys($error)) > 0) {
			$return_object['error'] = $error;
		}
	} else {
		$return_object['error'] = $error;
	}
	echo json_encode($return_object, JSON_UNESCAPED_UNICODE);
}

unset($parameters['uid']);

if (isset($parameters['sid'])) {
	$parameters['uid'] = Auth::getUserId($parameters['sid']);
	if (is_null($parameters['uid'])) {
		ErrorList::addError(106);
	}
	unset($parameters['sid']);
} else {
	ErrorList::addError(105);
	makeResponse([], ErrorList::makeAssoc());
}
