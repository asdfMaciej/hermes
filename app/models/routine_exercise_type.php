<?php
namespace Model;
class RoutineExerciseType extends \DBModel {
	protected static $table_name = "routine_exercise_types";
	protected static $primary_key = "routine_type_id";

	public $routine_type_id;
	public $routine_id;
	public $type_id;
	public $sets;
}
?>