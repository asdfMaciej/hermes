<?php
namespace Model;
class Gym extends \DBModel {
	protected static $table_name = "gyms";
	protected static $primary_key = "gym_id";

	public $gym_id;
	public $album_id;
	public $type_id;
	public $name;
	public $lat;
	public $long;

	public static function getExerciseRecords($database, $gym_id) {
		/*
		todo: subqueries arent supported in orm
		*/
		$rows = static::sql("
		SELECT e.workout_id, e.type_id, e.reps, e.weight, date(w.date) as date, w.gym_id, u.name, u.avatar, 
		et.exercise_type, et.show_duration, et.show_reps, et.show_weight
		FROM (
			SELECT ee.type_id, MAX(ee.weight) AS weight
			FROM gyms AS g
			INNER JOIN workouts AS w
			ON w.gym_id = g.gym_id
			INNER JOIN exercises AS ee
			ON ee.workout_id = w.workout_id AND failure = 0
			WHERE g.gym_id = :gym_id
			GROUP BY ee.type_id
		) AS wmax
		INNER JOIN exercises AS e
		ON e.weight = wmax.weight AND e.type_id = wmax.type_id
		INNER JOIN exercise_types AS et
		ON et.type_id = e.type_id
		INNER JOIN workouts AS w
		ON w.workout_id = e.workout_id
		INNER JOIN users AS u
		ON u.user_id = w.user_id
		GROUP BY e.type_id
		")
		->setParameter(":gym_id", $gym_id)
		->execute($database)
		->getAll();

		return $rows;
	}

	public static function getFrequenters($database, $gym_id) {
		$rows = static::select([
			"count(*) as visits",
			User::class => ["name", "avatar", "user_id"]
		])
		->from(Workout::class)
		->innerJoin(User::class, "user_id")
		->where("Workout.gym_id = :gym_id")
		->setParameter(":gym_id", $gym_id)
		->groupBy("User.user_id")
		->orderBy("visits", "desc")
		->limit(5)
		->execute($database)
		->getAll();
		return $rows;
	}
}
?>