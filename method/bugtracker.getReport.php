<?php

require_once 'api_init.php';

$responseConstructor = new ServerResponse($_POST, $_GET, $_COOKIE);
$responseConstructor->checkAuthentication(); 
$responseConstructor->setRequiredParameters(['post_id']);
$responseConstructor->checkParameters(); 
$responseConstructor->makeResponse(Bugtracker::getPost($responseConstructor->requestParameters['post_id'], false));
