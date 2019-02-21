<?php

require_once __DIR__ . '/api_init.php';

$responseConstructor = new ServerResponse($_POST, $_GET, $_COOKIE);
$responseConstructor->checkAuthentication(); 
$responseConstructor->setRequiredParameters(['partition_id', 'topic_id', 'topic_level', 'lesson_number', 'exercise_number']);
$responseConstructor->checkParameters();
$responseConstructor->makeResponse(LessonsList::getExercise(
	$responseConstructor->requestParameters['partition_id'],
	$responseConstructor->requestParameters['topic_id'],
	$responseConstructor->requestParameters['topic_level'],
	$responseConstructor->requestParameters['lesson_number'],
	$responseConstructor->requestParameters['exercise_number']
));
