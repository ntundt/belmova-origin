<?php

require_once 'api_init.php';
require_once __DIR__ . '/../engine/user.php';

if (isset($parameters['uid'])) {
	$user = new User($parameters['uid']);
	makeResponse($user->getLessonsList());
}