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
$s = $db->prepare("insert into workouts (user_id, gym_id, name) values (?, ?, ?)");
/*

*/
$post = [
	"workout" => [
		"gym_id" => 1,
		"user_id" => 16,
		"name" => "Kwarantanna dobrze wykorzystana"
	],
	"exercises" => [
		[
			"type_id" => 1,
			"reps" => 5,
			"weight" => 80
		],
		[
			"type_id" => 1,
			"reps" => 5,
			"weight" => 80
		],
		[
			"type_id" => 2,
			"reps" => 8,
			"weight" => 10
		]
	]
];



$db->beginTransaction();
try {
	\Model\Workout::fromArray($post["workout"])->save($db);
	$workout_id = $db->lastInsertId();
	foreach ($post["exercises"] as $exercise) {
		$exercise["workout_id"] = $workout_id;
		\Model\Exercise::fromArray($exercise)->save($db);
	}
} catch (\Exception $e) {
	$db->rollBack();
	throw $e;
}

$db->commit();
?>