<?php

header('Content-Type: text/json; charset=utf-8');

require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/../engine/user.php';

if (count($_POST) > 0) {
	$parameters = $_POST;
} else {
	$parameters = $_GET;
}

function makeResponse($response = [], $error = []) {
	$return = [];
	if (count(array_keys($response)) > 0) {
		$return['response'] = $response;
	}
	if (count(array_keys($error)) > 0) {
		$return['error'] = $error;
	}
	echo json_encode($return, JSON_UNESCAPED_UNICODE);
}

if (isset($parameters['sid'])) {
	require_once __DIR__ . '/../engine/auth.php';

	$auth = new Auth();

	$parameters['uid'] = $auth->getUserId($parameters['sid']);
	unset($parameters['sid']);
} else {
	require_once __DIR__ . '/../engine/error.php';
	
	$error = new OutputError(105);
	makeResponse([], $error->makeAssoc());
}
