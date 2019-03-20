<?php

require_once __DIR__ . '/config.php';

function redirect($to) {
	http_response_code(301);
	header('Location: http://localhost/' . $to);
}

if (isset($_COOKIE['sid'])) {
	$user = new User(Auth::getUserId($_COOKIE['sid']));
} else {
	$user = false;
}

if (strpos($_SERVER['REQUEST_URI'], 'bugtracker') !== false) {
	if (isset($_GET['act'])) {
		switch ($_GET['act']) {
		case 'add':
			if ($user === false) {
				redirect('bugtracker');
				die;
			}
			include __DIR__ . '/markup/bugtracker_add.phtml';
			break;
		case 'view':
			include __DIR__ . '/markup/bugtracker_view.phtml';
			break;
		default:
			include __DIR__ . '/markup/bugtracker.phtml';
			break;
		}
	} else {
		include __DIR__ . '/markup/bugtracker.phtml';
	}
} else if (strpos($_SERVER['REQUEST_URI'], 'login') !== false) {
	if ($user !== false) {
		redirect('index');
		die;
	}
	include __DIR__ . '/markup/login.phtml';
} else if (strpos($_SERVER['REQUEST_URI'], 'index') !== false) {
	include __DIR__ . '/markup/index.phtml';
} else if (strpos($_SERVER['REQUEST_URI'], 'learn') !== false) {
	if ($user === false) {
		redirect('index');
		die;
	}
	if (isset($_GET['act'])) {
		switch ($_GET['act']) {
		case 'lesson':
			include __DIR__ . '/markup/learn_lesson.phtml';
			break;
		case 'constructor':
			include __DIR__ . '/markup/learn_constructor.phtml';
			break;
		default:
			include __DIR__ . '/markup/learn.phtml';
			break;
		}
	} else {
		include __DIR__ . '/markup/learn.phtml';
	}
} else if (strpos($_SERVER['REQUEST_URI'], 'oauth') !== false) {
	include __DIR__ . '/oauth.php';
} else if (strpos($_SERVER['REQUEST_URI'], 'test') !== false) {
	if ($user === false) {
		redirect('login');
		die;
	}
	include __DIR__ . '/markup/test.phtml';
} else if (strcmp($_SERVER['REQUEST_URI'], '/') === 0) {
	include __DIR__ . '/markup/index.phtml';
} else {
	http_response_code(404);
	include __DIR__ . '/markup/404.phtml';
}
