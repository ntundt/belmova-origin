<?php

require_once 'api_init.php';

$responseConstructor = new ServerResponse($_POST, $_GET, $_COOKIE);
$client = new User($responseConstructor->checkAuthentication());
$responseConstructor->setRequiredParameters(['partition_id', 'topic_id', 'topic_level', 'lesson_number']);
$responseConstructor->checkParameters();
$responseConstructor->makeResponse($client->getLesson(
	$responseConstructor->requestParameters['partition_id'], 
	$responseConstructor->requestParameters['topic_id'], 
	$responseConstructor->requestParameters['topic_level'], 
	$responseConstructor->requestParameters['lesson_number']
));
