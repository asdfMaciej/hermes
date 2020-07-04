<?php
namespace Model;
class WorkoutReaction extends \DBModel {
	protected static $table_name = "workout_reactions";
	protected static $primary_key = "workout_id";
	// todo: multiple primary keys

	public $user_id;
	public $workout_id;
	public $type_id;

	public static function react($database, $user_id, $workout_id) {
		static::sql("
			INSERT INTO workout_reactions
			(workout_id, user_id, type_id)
			VALUES 
			(:workout_id, :user_id, 1)
		")
		->setParameter(":workout_id", $workout_id)
		->setParameter(":user_id", $user_id)
		->execute($database);
	}

	public static function unreact($database, $user_id, $workout_id) {
		static::sql("
			DELETE FROM workout_reactions
			WHERE user_id = :user_id and workout_id = :workout_id
		")
		->setParameter(":workout_id", $workout_id)
		->setParameter(":user_id", $user_id)
		->execute($database);
	}
}
?>