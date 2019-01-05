<?php

require_once 'api_init.php';

$continue = true;
$error = [];

$required = ['partition_id', 'topic_id', 'topic_level', 'lesson_number'];
for ($i = 0; $i < count($required); $i++) {
	if (!isset($parameters[$required[$i]])) {
		$continue = false;
		$error = new OutputError(108);
	}
}

$int_pars = ['partition_id', 'topic_id', 'topic_level', 'lesson_number'];
for ($i = 0; $i < count($int_pars); $i++) {
	$parameters[$int_pars[$i]] = intval($parameters[$int_pars[$i]]);
}

if (isset($parameters['uid']) and $continue and !isset($error->errno)) {
	$user = new User($parameters['uid']);
	makeResponse($user->getLesson($parameters['partition_id'], $parameters['topic_id'], $parameters['topic_level'], $parameters['lesson_number']));
} else {
	makeResponse([], $error->makeAssoc());
}
