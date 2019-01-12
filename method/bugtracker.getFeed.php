<?php

require_once 'api_init.php';

if (isset($parameters['uid'])) {
	$feed = Bugtracker::getFeed();
	makeResponse($feed);
}