<?php

require_once 'api_init.php';

$responseConstructor = new ServerResponse($_POST, $_GET, $_COOKIE);
$client = new User($responseConstructor->checkAuthentication());
$responseConstructor->setRequiredParameters([
	[
		'partition_id', 
		'topic_id', 
		'topic_level', 
		'lesson_number'
	],
	[
		'lesson_id'
	]
]);
switch ($responseConstructor->checkParameters()) {
case 0:
	$responseConstructor->makeResponse($client->getLesson(
		$responseConstructor->requestParameters['partition_id'], 
		$responseConstructor->requestParameters['topic_id'], 
		$responseConstructor->requestParameters['topic_level'], 
		$responseConstructor->requestParameters['lesson_number']
	));
	break;
case 1:
	$responseConstructor->makeResponse($client->getLessonById(
		$responseConstructor->requestParameters['lesson_id']
	));
	break;
}