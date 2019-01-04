<?php

// Database options
define('DB_HOST', 'localhost');
define('DB_LOGIN', 'nikita');
define('DB_PASSWORD', 'forestrys');
define('DB_NAME', 'belmova');
define('DB_TABLE_PREFIX', 'bm_');

define('URL_TO_DIR', 'http://localhost/belamova');

define('SERVICE_NAME', 'Sample service name');

// Classes that could be needed during execution
require_once __DIR__ . '/engine/error.php';
require_once __DIR__ . '/engine/lang.php';
require_once __DIR__ . '/engine/db.php';
require_once __DIR__ . '/engine/arr.php';


Lang::tryToSetLang($_SERVER['HTTP_ACCEPT_LANGUAGE'], $_COOKIE);