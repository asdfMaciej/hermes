<?php
namespace Model;
class Exercise extends \DBModel {
	protected static $table_name = "exercises";
	protected static $primary_key = "exercise_id";

	public $exercise_id;
	public $workout_id;
	public $type_id;
	public $reps;
	public $weight;
	public $duration;
	public $failure;
}
?>