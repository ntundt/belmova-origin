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
}