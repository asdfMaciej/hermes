<?php
/*
 * File used to quickly make internal changes. 
 * Creating respective templates, controllers and routing
 * 	is an unnecessary waste of time for those tasks.
 * Debug only.
*/

const IMPORT_MODELS = False;
const AS_RAW_TEXT = True;

include_once __DIR__ . "/application/config.php";

if (!DEBUG) {
	echo "debug turned off";
	die();
}

include_once ROOT_PATH . "/application/framework/include.php";
include_once ROOT_PATH . "/application/debug_tools.php";

$db_class = new DBClass();
$db = $db_class->getConnection();

$test = new DBModelTest();
foreach (glob(ROOT_PATH . "/application/models/*.php") as $model_path) {
	//out("Checking: $model_path");
	$result = $test->run($model_path);
	out($result[1]);
	out();
}

?>