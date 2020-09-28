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

    public static function getGymFrequency($database, $user_id) {
        $rows = static::sql("
            SELECT date(DATE) AS date, COUNT(workout_id) AS count
            FROM workouts
            WHERE user_id = :user_id
            GROUP BY date(DATE)
            ")
            ->setParameter(":user_id", $user_id)
            ->execute($database)
            ->getAll();

        return $rows;
    }

    public function register($db) {
		try {
			$this->password = password_hash($this->password, PASSWORD_DEFAULT);
			return self::save($db);
		} catch (\Exception $e) {
			return false;
		}
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
		$row = static::sql("
SELECT 
	User.user_id, User.login, User.name, User.register_date, User.avatar, 
	EXISTS(SELECT 0 FROM followers WHERE user_id = User.user_id AND follower_id = :viewer_id) AS following,
	(SELECT COUNT(follower_id) AS n FROM followers WHERE user_id = User.user_id) AS followers_count,
	(SELECT COUNT(user_id) AS n FROM followers WHERE follower_id = User.user_id) AS following_count,
	(SELECT COUNT(workout_id) AS n FROM workouts WHERE user_id = User.user_id) AS workout_count
FROM `users` AS User
WHERE User.user_id = :id
			")
				->setParameter(":id", $id)
				->setParameter(":viewer_id", $viewer_id)
				->execute($database)
				->getRow();

		return $row;
	}

	protected static function getProfilesQuery() {
        return "
SELECT 
	User.user_id, User.login, User.name, User.register_date, User.avatar, 
	COALESCE(follow.following, 0) AS following, 
	coalesce(stats.frequency, 0) AS frequency, stats.last_workout
FROM `users` AS User

LEFT JOIN (
	SELECT 
		w.user_id, COUNT(w.workout_id) as frequency, MAX(w.added) AS last_workout 
	FROM `workouts` AS w
	GROUP BY w.user_id
) AS stats
ON stats.user_id = User.user_id

LEFT JOIN (
	SELECT 
		1 AS following, user_id
	FROM followers as f 
	WHERE follower_id = :viewer_id 
) AS follow
ON follow.user_id = User.user_id
        ";
    }
	public static function searchProfiles($database, $query, $viewer_id) {
		$row = static::sql(static::getProfilesQuery() . " WHERE User.name LIKE :query")
				->setParameter(":query", $query)
				->setParameter(":viewer_id", $viewer_id)
				->execute($database)
				->getAll();

		return $row;
	}

    public static function getFollowedProfiles($database, $user_id, $viewer_id) {
        $row = static::sql(static::getProfilesQuery() . "
WHERE User.user_id IN (
	SELECT user_id from followers AS Follower
	Where Follower.follower_id = :user_id
)
         ")
            ->setParameter(":user_id", $user_id)
            ->setParameter(":viewer_id", $viewer_id)
            ->execute($database)
            ->getAll();

        return $row;
    }

    public static function getProfileFollowers($database, $user_id, $viewer_id) {
        $row = static::sql(static::getProfilesQuery() . "
WHERE User.user_id IN (
	SELECT follower_id from followers AS Follower
	Where Follower.user_id = :user_id
)
         ")
            ->setParameter(":user_id", $user_id)
            ->setParameter(":viewer_id", $viewer_id)
            ->execute($database)
            ->getAll();

        return $row;
    }

	public static function getStatistics($database, $id) {
		$row = static::select([
					"User.user_id",
					"COUNT(Workout.workout_id) as workout_count",
					"MAX(Workout.date) as workout_last_date"
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
		->execute($database);
	}
}
?>