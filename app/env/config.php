<?php
$production = false;

define("SESSION_NAME", "hermes");

if ($production) {
	define("DB_HOST", "localhost");
	define("DB_USERNAME", "root");
	define("DB_PASSWORD", "");
	define("DB_DATABASE", "hermes");

	define('ROOT_PATH', '/app');
	define('PATH_PREFIX', '/app');

	define('DEBUG', False);
} else {
	define("DB_HOST", "localhost");
	define("DB_USERNAME", "root");
	define("DB_PASSWORD", "");
	define("DB_DATABASE", "hermes");

	define('ROOT_PATH', $_SERVER["DOCUMENT_ROOT"] . "/app");
	define('PATH_PREFIX', "/app");
	define('DEBUG', True);
}

session_name(SESSION_NAME);
session_start();
?>