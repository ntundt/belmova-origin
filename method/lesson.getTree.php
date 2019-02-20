<?php

require_once 'api_init.php';

$responseConstructor = new ServerResponse($_POST, $_GET, $_COOKIE);
$responseConstructor->checkAuthentication(); 
$responseConstructor->makeResponse(LessonsList::getTree());

// if (isset($parameters['uid'])) {
// 	makeResponse(LessonsList::getTree(), ErrorList::makeAssoc());
// } else {
// 	makeResponse([], ErrorList::makeAssoc());
// }