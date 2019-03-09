<?php

require_once 'api_init.php';

$responseConstructor = new ServerResponse($_POST, $_GET, $_COOKIE);
$responseConstructor->setRequiredParameters(['text', 'reply_to']);
$responseConstructor->checkParameters(); 
$responseConstructor->makeResponse(Bugtracker::addComment(
	$responseConstructor->requestParameters, 
	$responseConstructor->checkAuthentication()
));
