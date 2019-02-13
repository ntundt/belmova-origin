<?php

header('Content-Type: text/json; charset=utf-8');

require_once __DIR__ . '/config.php';

$already_sent = false;

if (0 < count($_POST)) {
	$parameters = $_POST;
} else {
	$parameters = $_GET;
}

function makeResponse($response = [], $error = []) {
	global $already_sent;
	if (!$already_sent) {
		$return_object = [];
		if (0 === strcmp(gettype($response), 'array')) {
			if (0 < count(array_keys($response))) {
				$return_object['response'] = $response;
			}
		} else {
			$return_object['response'] = $response;
		}
		if (0 === strcmp(gettype($error), 'array')) {
			if (0 < count(array_keys($error))) {
				$return_object['error'] = $error;
			}
		} else {
			$return_object['error'] = $error;
		}
		echo json_encode($return_object, JSON_UNESCAPED_UNICODE);
		$already_sent = true;
	}
}

if (isset($parameters['user_id']) and isset($parameters['access_token']) and isset($parameters['email'])) {
	makeResponse(['sid' => Auth::authByVkId($parameters['user_id'], $parameters['access_token'])], ErrorList::makeAssoc());
} else {
	ErrorList::addError(106);
	makeResponse([], ErrorList::makeAssoc());
}
