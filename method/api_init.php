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
	if (count(array_keys($response)) > 0) {
		$return_object['response'] = $response;
	}
	if (count(array_keys($error)) > 0) {
		$return_object['error'] = $error;
	}
	echo json_encode($return_object, JSON_UNESCAPED_UNICODE);
}

if (isset($parameters['sid'])) {
	$parameters['uid'] = Auth::getUserId($parameters['sid']);
	unset($parameters['sid']);
} else {
	$error = new OutputError(105);
	makeResponse([], $error->makeAssoc());
}
