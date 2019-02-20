<?php

require_once 'api_init.php';

$responseConstructor = new ServerResponse($_POST, $_GET, $_COOKIE);
$user = new User($responseConstructor->checkAuthentication()); 
$responseConstructor->makeResponse($user->getLessonsList());
