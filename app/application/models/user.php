<?php
namespace Model;
class User extends \DBModel {
	protected static $table_name = "users";
	protected static $primary_key = "user_id";

	public $user_id;
	public $login;
	public $password;
	public $name;
	public $register_date;
	public $avatar;

	public function register($db) {
		$this->password = password_hash($this->password, PASSWORD_DEFAULT);
		return self::save($db);
	}

	public static function login($db, $login, $password) {
		$user = static::getSingleItem($db, ["login" => $login]);

		if (!$user->user_id)
			return false;

		if (password_verify($password, $user->password))
			return $user;
		else
			return false;
	}

	public static function getProfileById($database, $id, $viewer_id) {
		$row = static::select([
					static::class => [
						"user_id", "login", "name", "register_date", "avatar"
					],
					"EXISTS(SELECT 0 FROM followers WHERE user_id = :id AND follower_id = :viewer_id) AS following"
				])
				->from(static::class)
				->where("User.user_id = :id")
				->setParameter(":id", $id)
				->setParameter(":viewer_id", $viewer_id)
				->execute($database)
				->getRow();

		return $row;
	}

	public static function getStatistics($database, $id) {
		$row = static::select([
					"User.user_id",
					"COUNT(Workout.workout_id) as workout_count",
					"date(MAX(Workout.date)) as workout_last_date"
				])
				->from(static::class)
				->where("User.user_id = :id")
				->leftJoin(Workout::class, "user_id")
				->groupBy("User.user_id")
				->setParameter(":id", $id)
				->execute($database)
				->getRow();

		return $row;
	}

	public static function follow($database, $follower_id, $following_id) {
		if ($follower_id == $following_id)
			return;

		static::sql("
			INSERT INTO followers
			(user_id, follower_id)
			VALUES 
			(:following_id, :follower_id)
		")
		->setParameter(":follower_id", $follower_id)
		->setParameter(":following_id", $following_id)
		->execute($database);
	}

	public static function unfollow($database, $follower_id, $following_id) {
		if ($follower_id == $following_id)
			return;

		static::sql("
			DELETE FROM followers
			WHERE user_id = :following_id and follower_id = :follower_id
		")
		->setParameter(":follower_id", $follower_id)
		->setParameter(":following_id", $following_id)
		->debug()
		->execute($database);
	}
}
?>