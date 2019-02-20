<?php

require_once 'api_init.php';

$responseConstructor = new ServerResponse($_POST, $_GET, $_COOKIE);
$responseConstructor->checkAuthentication(); 
$responseConstructor->setRequiredParameters(['description']);
$responseConstructor->checkParameters(); 
$responseConstructor->makeResponse(Bugtracker::addPost($responseConstructor->requestParameters, $responseConstructor->requestParameters['uid']));


// $continue = true;

// $required = ['description'];
// for ($i = 0; $i < count($required); $i++) {
// 	if (!isset($parameters[$required[$i]])) {
// 		$continue = false;
// 		ErrorList::addError(108);
// 	}
// }

// if (isset($parameters['uid']) and $continue) {
// 	makeResponse(Bugtracker::addPost($parameters, $parameters['uid']), ErrorList::makeAssoc());
// } else {
// 	makeResponse([], ErrorList::makeAssoc());
// }
