<?php

require_once 'api_init.php';

$responseConstructor = new ServerResponse($_POST, $_GET, $_COOKIE);
$responseConstructor->checkAuthentication(); 
$responseConstructor->setRequiredParameters(['partition_id', 'topic_id', 'topic_level', 'lesson_number', 'exercise_number', 'json_object']);
$responseConstructor->checkParameters(); 
$responseConstructor->makeResponse(LessonsList::setLesson(
	$responseConstructor->requestParameters['partition_id'], 
	$responseConstructor->requestParameters['topic_id'], 
	$responseConstructor->requestParameters['topic_level'], 
	$responseConstructor->requestParameters['lesson_number'], 
	$responseConstructor->requestParameters['exercise_number'], 
	$responseConstructor->requestParameters['json_object']
));
