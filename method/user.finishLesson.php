<?php

require_once 'api_init.php';

$responseConstructor = new ServerResponse($_POST, $_GET, $_COOKIE);
$client = new User($responseConstructor->checkAuthentication());
$responseConstructor->setRequiredParameters(['partition_id', 'topic_id', 'topic_level', 'lesson_number']);
$responseConstructor->checkParameters();
$responseConstructor->makeResponse($client->finishLesson(
	$responseConstructor->requestParameters['partition_id'], 
	$responseConstructor->requestParameters['topic_id'], 
	$responseConstructor->requestParameters['topic_level'], 
	$responseConstructor->requestParameters['lesson_number']
));

// $continue = true;
// $error = [];

// $required = ['partition_id', 'topic_id', 'topic_level', 'lesson_number'];
// for ($i = 0; $i < count($required); $i++) {
// 	if (!isset($parameters[$required[$i]])) {
// 		$continue = false;
// 		ErrorList::addError(108);
// 	}
// }

// $int_pars = ['partition_id', 'topic_id', 'topic_level', 'lesson_number'];
// for ($i = 0; $i < count($int_pars); $i++) {
// 	$parameters[$int_pars[$i]] = intval($parameters[$int_pars[$i]]);
// }

// if (isset($parameters['uid']) and $continue and !isset($error->errno)) {
// 	$user = new User($parameters['uid']);
// 	$response_object = $user->finishLesson($parameters['partition_id'], $parameters['topic_id'], $parameters['topic_level'], $parameters['lesson_number']);
// 	makeResponse($response_object, ErrorList::makeAssoc());
// } else {
// 	makeResponse([], ErrorList::makeAssoc());
// }
