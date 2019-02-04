<?php

require_once 'config.php';

if (isset($_COOKIE['sid'])) {
	$user = new User(Auth::getUserId($_COOKIE['sid']));
} else {
	$user = false;
}

if (strpos($_SERVER['REQUEST_URI'], 'bugtracker') !== false) {
	if (isset($_GET['act'])) {
		switch ($_GET['act']) {
		case 'add':
			include 'markup/bugtracker_add.phtml';
			break;
		case 'view':
			include 'markup/bugtracker_view.phtml';
			break;
		default:
			include 'markup/bugtracker.phtml';
			break;
		}
	} else {
		include 'markup/bugtracker.phtml';
	}
} else if (strpos($_SERVER['REQUEST_URI'], 'login') !== false) {
	include 'markup/login.phtml';
} else if (strpos($_SERVER['REQUEST_URI'], 'index') !== false) {
	include 'markup/index.phtml';
} else if (strpos($_SERVER['REQUEST_URI'], 'learn') !== false) {
	if (isset($_GET['act'])) {
		switch ($_GET['act']) {
		case 'lesson':
			include 'markup/learn_lesson.phtml';
			break;
		case 'constructor':
			include 'markup/learn_constructor.phtml';
			break;
		default:
			include 'markup/learn.phtml';
			break;
		}
	} else {
		include 'markup/learn.phtml';
	}
} else if (strpos($_SERVER['REQUEST_URI'], 'oauth') !== false) {
	include 'oauth.php';
} else {
	include 'markup/index.phtml';
}