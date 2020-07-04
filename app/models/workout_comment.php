<?php
namespace Model;
class WorkoutComment extends \DBModel {
	protected static $table_name = "workout_comments";
	protected static $primary_key = "comment_id";

	public $comment_id;
	public $workout_id;
	public $user_id;
	public $comment;
	public $created;
}
?>