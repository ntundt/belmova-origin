<?php

require_once 'api_init.php';

$responseConstructor = new ServerResponse($_POST, $_GET, $_COOKIE);
$responseConstructor->checkAuthentication(); 
$responseConstructor->setRequiredParameters(['description']);
$responseConstructor->checkParameters(); 
$responseConstructor->makeResponse(Bugtracker::addPost($responseConstructor->requestParameters, $responseConstructor->requestParameters['uid']));
