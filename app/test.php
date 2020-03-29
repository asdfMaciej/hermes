<?php
/*
 * File used to quickly make internal changes. 
 * Creating respective templates, controllers and routing
 * 	is an unnecessary waste of time for those tasks.
 * Debug only.
*/

const IMPORT_MODELS = False;
const AS_RAW_TEXT = True;

include_once __DIR__ . "/env/config.php";
include_once ROOT_PATH . "/framework/include.php";
include_once ROOT_PATH . "/application/debug_tools.php";

$test = new DBModelTest();
$success = True;
foreach (glob(ROOT_PATH . "/application/models/*.php") as $model_path) {
	out("Checking: $model_path");
	$result = $test->run($model_path);
	$success = $success && $result[0];
	out($result[1]);
	out();
}

out("---");
out($success ? "All tests were succesful!" : "Some tests have failed.");
?>