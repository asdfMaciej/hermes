<?php
namespace Model;
class ExerciseType extends \DBModel {
	protected static $table_name = "exercise_types";
	protected static $primary_key = "type_id";

	public $type_id;
	public $category_id;
	public $exercise_type;
	public $show_duration;
	public $show_reps;
	public $show_weight;
}
?>