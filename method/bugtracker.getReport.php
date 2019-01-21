<?php

require_once 'api_init.php';

$continue = true;

$required = ['post_id'];
for ($i = 0; $i < count($required); $i++) {
	if (!isset($parameters[$required[$i]])) {
		$continue = false;
		ErrorList::addError(108);
	}
}

$int_pars = ['post_id'];
for ($i = 0; $i < count($int_pars); $i++) {
	$parameters[$int_pars[$i]] = intval($parameters[$int_pars[$i]]);
}

if (isset($parameters['uid']) and $continue) {
	makeResponse(Bugtracker::getPost($parameters['post_id'], false), ErrorList::makeAssoc());
} else {
	makeResponse([], ErrorList::makeAssoc());
}