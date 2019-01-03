<?php

header('Content-Type: text/json; charset=utf-8');

require_once __DIR__ . '/../config.php';

if (count($_POST) > 0) {
	$parameters = $_POST;
} else {
	$parameters = $_GET;
}

function makeResponse($response = [], $error = ['errno' => true]) {
	$return = ['response' => $response, 'error' => $error];
	echo json_encode($return, JSON_UNESCAPED_UNICODE);
}

if (isset($parameters['login']) && isset($parameters['password'])) {
	require_once __DIR__.'/../engine/auth.php';

	$auth = new Auth();

	makeResponse(['sid' => $auth->userLogin($parameters['login'], $parameters['password'])]);
} else {
	$err = new OutputError(107);
	makeResponse([], $err->makeAssoc());
}