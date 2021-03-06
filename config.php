<?php

// Database options
define('DB_HOST', 'localhost');
define('DB_LOGIN', 'root');
define('DB_PASSWORD', 'forestrys');
define('DB_NAME', 'bm');
define('DB_TABLE_PREFIX', 'bm_');

define('VK_ACCESS_TOKEN', '4ccc6c984ccc6c984ccc6c984a4ca401ec44ccc4ccc6c9810991b8c237d255dcdf1a0b1');
define('VK_API_VERSION', '5.92');

define('URL_TO_DIR', 'http://192.168.100.16/work/');
define('DOMAIN', '192.168.100.16');

define('SERVICE_NAME', 'Sample service name');

define('DEBUG', true);

// Classes that could be needed during execution
require_once __DIR__ . '/engine/error.php';
require_once __DIR__ . '/engine/lang.php';
require_once __DIR__ . '/engine/db.php';
require_once __DIR__ . '/engine/arr.php';
require_once __DIR__ . '/engine/user.php';
require_once __DIR__ . '/engine/auth.php';
require_once __DIR__ . '/engine/lesson.php';
require_once __DIR__ . '/engine/bugtracker.php';
require_once __DIR__ . '/engine/vkrequest.php';
require_once __DIR__ . '/engine/debug.php';
require_once __DIR__ . '/engine/date.php';

// Initialisation
Database::init();
Database::setTablePrefix(DB_TABLE_PREFIX);
Lang::tryToSetLang($_SERVER['HTTP_ACCEPT_LANGUAGE'], $_COOKIE);
