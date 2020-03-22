<?php
namespace Model;
class Workout extends \DBModel {
	protected static $table_name = "workouts";
	protected static $primary_key = "workout_id";

	public $workout_id;
	public $user_id;
	public $gym_id;
	public $name;
	public $date;
	public $added;
	public $modified;

	public static function getNewsfeedList($database) {
		$rows = static::select("w.workout_id, w.name, w.date, 
				user.name as user_name, gym.name as gym_name")
				->from(static::class, "w")
				->innerJoin(User::class, "user", "user.user_id = w.user_id")
				->innerJoin(Gym::class, "gym", "gym.gym_id = w.gym_id")
				->orderBy("w.workout_id", "desc")
				->execute($database)
				->getAll();

		return $rows;
	}

	public static function getById($database, $id) {
		$row = static::select("w.workout_id, w.name, w.date, 
				user.name as user_name, gym.name as gym_name, gym.gym_id")
				->from(static::class, "w")
				->innerJoin(User::class, "user", "user.user_id = w.user_id")
				->innerJoin(Gym::class, "gym", "gym.gym_id = w.gym_id")
				->orderBy("w.workout_id", "desc")
				->where("w.workout_id = :id")
				->setParameter(":id", $id)
				->execute($database)
				->getRow();

		return $row;
	}

	public function getExercises($database, $id) {
		$row = static::select("e.*, et.*")
				->from(static::class, "w")
				->leftJoin(Exercise::class, "e", "w.workout_id = e.workout_id")
				->innerJoin(ExerciseType::class, "et", "et.type_id = e.type_id")
				->where("w.workout_id = :id")
				->setParameter(":id", $id)
				->execute($database)
				->getAll();

		return $row;
	}
}
?>