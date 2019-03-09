<?php

require_once 'api_init.php';

$responseConstructor = new ServerResponse($_POST, $_GET, $_COOKIE);
$responseConstructor->setRequiredParameters(['post_id']);
$responseConstructor->checkParameters();
$user = new User($responseConstructor->checkAuthentication(true));
$responseConstructor->makeResponse(
	Bugtracker::getPost(
		$responseConstructor->requestParameters['post_id'], 
		false,
		$user
	)
);
