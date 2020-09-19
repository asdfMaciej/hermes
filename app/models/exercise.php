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

    public static function getPastExercises($database, $user_id,  $type_id) {
        $rows = static::sql("
		SELECT e.exercise_id, e.reps, e.weight, e.duration

        FROM (
            SELECT MAX(e.workout_id) AS workout_id
            FROM workouts AS w
            INNER JOIN exercises AS e
            ON e.workout_id = w.workout_id 
                AND e.type_id = :type_id
            WHERE w.user_id = :user_id
        ) AS freq
        
        INNER JOIN exercises AS e
        ON e.workout_id = freq.workout_id
        AND e.type_id = :type_id
		")
            ->setParameters([
                ":type_id" => $type_id,
                ":user_id" => $user_id
            ])
            ->execute($database)
            ->getAll();

        return $rows;
    }

    public static function getWithTypes($database, $workout_id) {
        $rows = static::select([
            static::class => "*",
            ExerciseType::class => ["*"]
        ])
            ->from(static::class)
            ->innerJoin(ExerciseType::class, "type_id")
            ->where("Exercise.workout_id = :workout_id")
            ->setParameter(":workout_id", $workout_id)
            ->execute($database)
            ->getAll();

        return $rows;
    }
}
?>