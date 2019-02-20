<?php

require_once 'api_init.php';

$responseConstructor = new ServerResponse($_POST, $_GET, $_COOKIE);
$responseConstructor->setRequiredParameters(['login', 'password']);
$responseConstructor->checkParameters(); 
$responseConstructor->makeResponse(['sid' => Auth::userLogin(
	$responseConstructor->requestParameters['login'],
	$responseConstructor->requestParameters['password']
)]);
