<?php

require_once 'api_init.php';

if (isset($parameters['uid'])) {
	makeResponse(LessonsList::getTree(), ErrorList::makeAssoc());
} else {
	makeResponse([], ErrorList::makeAssoc());
}