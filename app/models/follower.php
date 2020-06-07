<?php
namespace Model;
class Follower extends \DBModel {
	protected static $table_name = "followers";

	// todo: ORM should support multiple primary keys
	protected static $primary_key = "user_id";

	public $user_id;
	public $follower_id;
}
?>