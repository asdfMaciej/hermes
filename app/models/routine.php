<?php
namespace Model;
class Routine extends \DBModel {
	protected static $table_name = "routines";
	protected static $primary_key = "routine_id";

	public $routine_id;
	public $user_id;
	public $created;
	public $name;

	public static function getExerciseTypes($database, $id) {
		$rows = static::select([
			RoutineExerciseType::class => "*",
			ExerciseType::class => "*"
		])
		->from(RoutineExerciseType::class)
		->where("RoutineExerciseType.routine_id = :id")
		->innerJoin(ExerciseType::class, "type_id")
		->orderBy("RoutineExerciseType.routine_type_id")
		->setParameter(":id", $id)
		->execute($database)
		->getAll();

		return $rows;
	}

	public static function getRoutines($database) {
		$rows = static::select([
			static::class => "*",
			User::class => ["name as user_name", "login", "avatar"]
		])
		->from(static::class)
		->innerJoin(User::class, "user_id")
		->execute($database)
		->getAll();

		return $rows;
	}
}
?>