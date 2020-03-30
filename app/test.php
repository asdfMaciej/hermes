<?php
/*
 * File used to quickly make internal changes. 
 * Creating respective templates, controllers and routing
 * 	is an unnecessary waste of time for those tasks.
 * Debug only.
*/

const IMPORT_MODELS = True;
const AS_RAW_TEXT = True;

include_once __DIR__ . "/env/config.php";
include_once ROOT_PATH . "/framework/include.php";
include_once ROOT_PATH . "/application/debug_tools.php";

$db_class = new DBClass();
$db = $db_class->getConnection();
?>