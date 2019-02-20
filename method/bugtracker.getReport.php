<?php

require_once 'api_init.php';

$responseConstructor = new ServerResponse($_POST, $_GET, $_COOKIE);
$responseConstructor->checkAuthentication(); 
$responseConstructor->setRequiredParameters(['post_id']);
$responseConstructor->checkParameters(); 
$responseConstructor->makeResponse(Bugtracker::getPost($responseConstructor->requestParameters['post_id'], false));


// $continue = true;

// $required = ['post_id'];
// for ($i = 0; $i < count($required); $i++) {
// 	if (!isset($parameters[$required[$i]])) {
// 		$continue = false;
// 		ErrorList::addError(108);
// 	}
// }

// $int_pars = ['post_id'];
// for ($i = 0; $i < count($int_pars); $i++) {
// 	$parameters[$int_pars[$i]] = intval($parameters[$int_pars[$i]]);
// }

// if (isset($parameters['uid']) and $continue) {
// 	makeResponse(Bugtracker::getPost($parameters['post_id'], false), ErrorList::makeAssoc());
// } else {
// 	makeResponse([], ErrorList::makeAssoc());
// }
