<?php

require_once 'api_init.php';

$continue = true;

$required = ['description'];
for ($i = 0; $i < count($required); $i++) {
	if (!isset($parameters[$required[$i]])) {
		$continue = false;
		ErrorList::addError(108);
	}
}

if (isset($parameters['uid']) and $continue) {
	makeResponse(Bugtracker::addPost($parameters, $parameters['uid']), ErrorList::makeAssoc());
} else {
	makeResponse([], ErrorList::makeAssoc());
}
