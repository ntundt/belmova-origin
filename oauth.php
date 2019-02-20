<?php

require_once __DIR__ . '/config.php';
require_once __DIR__ . '/method/api_init.php';

$responseConstructor = new ServerResponse($_POST, $_GET, $_COOKIE);
$responseConstructor->setRequiredParameters(['user_id', 'access_token', 'email']);
$responseConstructor->checkParameters(); 
$responseConstructor->makeResponse(['sid' => Auth::authByVkId(
	$responseConstructor->requestParameters['user_id'],
	$responseConstructor->requestParameters['access_token']
)]);
