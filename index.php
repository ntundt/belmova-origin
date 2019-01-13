<?php

require_once 'config.php';

if (isset($_COOKIE['sid'])) {
	$user = new User(Auth::getUserId($_COOKIE['sid']));
} else {
	$user = false;
}

if (isset($_GET['activity'])) {
	switch ($_GET['activity']) {
	case 'bugtracker':
		include 'markup/bugtracker.phtml';
		break;
	case 'login':
		include 'markup/login.phtml';
		break; 
	}
} else {
	include 'markup/index.phtml';
}