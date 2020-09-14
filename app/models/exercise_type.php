<?php
namespace Model;
class ExerciseType extends \DBModel {
	protected static $table_name = "exercise_types";
	protected static $primary_key = "type_id";

	public $type_id;
	public $category_id;
	public $exercise_type;
    public $exercise_type_en;
	public $show_duration;
	public $show_reps;
	public $show_weight;

	public static function getExerciseTypes($database) {
        $rows = static::select([
            ExerciseType::class => "*"
        ])
        ->from(static::class)
        ->orderBy("ExerciseType.exercise_type")
        ->execute($database)
        ->getAll();

        return $rows;
    }

    public static function getWeightRecords($database, $type_id) {
	    // i had to replace any_value with max due to prior mysql versions support
	    $rows = static::sql("
	    SELECT 
	s.type_id, MAX(s.max_weight) AS max_weight, s.user_id,
	MAX(s.workout_id) AS workout_id, MAX(s.date) AS date,
	MAX(s.name) AS name, MAX(s.avatar) AS avatar
FROM (
SELECT 	
	e.type_id, max(e.weight) AS max_weight, SUM(e.weight * e.reps) AS volume, 
	w.user_id, w.workout_id, MAX(w.date) AS date,
	MAX(u.name) AS name, MAX(u.avatar) AS avatar
FROM exercises AS e
INNER JOIN workouts AS w 
	ON w.workout_id = e.workout_id
INNER JOIN users AS u
	ON u.user_id = w.user_id
WHERE 
	e.type_id = :type_id
GROUP BY 
	w.user_id, e.type_id, w.workout_id
) AS s

GROUP BY s.user_id, s.type_id
order by max_weight desc
")
            ->setParameter(":type_id", $type_id)
            ->execute($database)
            ->getAll();

	    return $rows;
    }

    public static function getUserExerciseHistory($database, $type_id, $user_id) {
    	$rows = static::sql("
	    SELECT 	
	e.type_id, max(e.weight) AS max_weight, SUM(e.weight * e.reps) AS volume, 
	w.user_id, w.workout_id, MAX(w.date) AS date
FROM exercises AS e
INNER JOIN workouts AS w 
	ON w.workout_id = e.workout_id
WHERE 
	e.type_id = :type_id AND w.user_id = :user_id
GROUP BY 
	w.user_id, e.type_id, w.workout_id
ORDER BY DATE asc
")
            ->setParameter(":type_id", $type_id)
            ->setParameter(":user_id", $user_id)
            ->execute($database)
            ->getAll();

	    return $rows;
    }
}
?>