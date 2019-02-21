<?php

require_once __DIR__ . '/api_init.php';

$responseConstructor = new ServerResponse($_POST, $_GET, $_COOKIE);
$responseConstructor->checkAuthentication(); 
$responseConstructor->makeResponse(LessonsList::getTree());
