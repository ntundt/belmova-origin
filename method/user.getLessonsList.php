<?php

require_once 'api_init.php';

if (isset($parameters['uid'])) {
	$user = new User($parameters['uid']);
	makeResponse($user->getLessonsList(), ErrorList::makeAssoc());
}
