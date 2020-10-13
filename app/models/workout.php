<?php
namespace Model;
class Workout extends \DBModel {
	protected static $table_name = "workouts";
	protected static $primary_key = "workout_id";

	public $workout_id;
	public $user_id;
	public $album_id;
	public $gym_id;
	public $title;
	public $date;
	public $added;
	public $modified;
    public $duration;
    public $description;

	protected static function queryNewsfeedList() {
		$query = static::select([
					static::class => ["workout_id", "title", "date"],
					User::class => ["name as user_name", "user_id", "avatar"],
					Gym::class => ["gym_id", "name as gym_name"]
				])
				->from(static::class)
				->innerJoin(User::class, "user_id")
				->innerJoin(Gym::class, "gym_id")
				->orderBy("Workout.workout_id", "desc");

		return $query;
	}

	protected static function getWorkoutWhereQuery($rows, $table_alias) {
		$workout_ids = [];
        $query_parameters = [];
        $ids_assoc_array = [];
        foreach ($rows as $row) {
            $workout_ids[] = $row["workout_id"];
            $ids_assoc_array[$row["workout_id"]] = [];
            $query_parameters[] = "?";
        }
        if ($query_parameters) {
            $query_parameters = "WHERE $table_alias.workout_id IN (" . implode(",", $query_parameters) . ')';
        } else {
            $query_parameters = "";
        }
        return compact("query_parameters", "workout_ids", "ids_assoc_array");
	}

	protected static function addSummaryToNewsfeed($database, $rows) {
        $q = static::getWorkoutWhereQuery($rows, 'e');
        $query_parameters = $q["query_parameters"];
        $workout_ids = $q["workout_ids"];
		$workout_stats = $q["ids_assoc_array"];

        $stats_query = "
	SELECT stats.*, et.exercise_type FROM (
	SELECT e.type_id, e.workout_id,
		COUNT(e.exercise_id) AS sets, 
	MAX(reps) AS max_reps, MIN(reps) AS min_reps,
	MIN(weight) AS min_weight, MAX(weight) AS max_weight,
	floor(SUM(reps * weight)) AS volume,
	MIN(duration) AS min_duration, MAX(duration) AS max_duration
	
	FROM exercises AS e
	$query_parameters
	GROUP BY e.type_id, e.workout_id
	ORDER BY e.workout_id, MAX(e.exercise_id)
) AS stats
INNER JOIN exercise_types AS et
ON et.type_id = stats.type_id
		";

        $stats_rows = static::sql($stats_query)
            ->setParameters($workout_ids)
            ->execute($database)
            ->getAll();

        foreach ($stats_rows as $row) {
            $workout_stats[$row["workout_id"]][] = $row;
        }

        // newsfeed rows
        foreach ($rows as &$row) {
            $row["summary"] = $workout_stats[$row["workout_id"]] ?? [];
        }

        return $rows;
    }

    public static function addPhotosToNewsfeed($database, $rows) {
    	$q = static::getWorkoutWhereQuery($rows, 'w');
        $query_parameters = $q["query_parameters"];
        $workout_ids = $q["workout_ids"];
		$workout_photos = $q["ids_assoc_array"];

		$photos_query = "
SELECT w.workout_id, p.*
	FROM workouts AS w 
INNER JOIN albums AS a
	ON a.album_id = w.album_id
INNER JOIN photos AS p
	ON p.album_id = a.album_id
$query_parameters
		";

		$photos = static::sql($photos_query)
			->setParameters($workout_ids)
			->execute($database)
			->getAll();

		foreach ($photos as $photo) {
			$workout_photos[$photo['workout_id']][] = $photo;
		}

		foreach ($rows as &$row) {
            $row["photos"] = $workout_photos[$row["workout_id"]] ?? [];
        }

        return $rows;
    }

    public static function addReactionsToNewsfeed($database, $rows) {
    	$q = static::getWorkoutWhereQuery($rows, 'Workout');
        $query_parameters = $q["query_parameters"];
        $workout_ids = $q["workout_ids"];
		$workout_photos = $q["ids_assoc_array"];

		$q = [];
		foreach ($workout_ids as $id) {
			$q[] = "(SELECT WorkoutReaction.workout_id, User.user_id, User.avatar
	FROM workout_reactions AS WorkoutReaction
INNER JOIN users as User 
	ON User.user_id = WorkoutReaction.user_id
where WorkoutReaction.workout_id = ?
limit 3)
";
		}

		$q = implode(" union all ", $q);

		if ($q) {
			$photos = static::sql($q)
				->setParameters($workout_ids)
				->execute($database)
				->getAll();
		} else {
			$photos = [];
		}
		

		foreach ($photos as $photo) {
			$workout_photos[$photo['workout_id']][] = $photo;
		}

		foreach ($rows as &$row) {
            $row["reactions_users"] = $workout_photos[$row["workout_id"]] ?? [];
        }

        return $rows;
    }

	public static function getNewsfeedList($database, $user_id, $parameters=[]) {
		$before_id = $parameters['before_id'] ?? '';
		$before_query = $before_id ? 'AND Workout.workout_id < :before_id' : '';

		$query_params = [":user_id" => $user_id];
		if ($before_id)
			$query_params[':before_id'] = $before_id;

		// todo: complicated ON queries arent supported by orm
		// todo: UNION isnt supported by orm
		$rows = static::sql("
		SELECT
		newsfeed.*,
		COALESCE(WorkoutCommentJoin.comments, 0) as comments,
		WorkoutComment.comment,
		WorkoutComment.created AS comment_created,
		User.name as comment_user_name, User.user_id AS comment_user_id, User.avatar AS comment_avatar
		

		FROM (
			SELECT 
				Workout.workout_id, Workout.title, Workout.date,  Workout.duration, Workout.description,
				User.name as user_name, User.user_id, User.avatar, 
				Gym.gym_id, Gym.name as gym_name,
				COUNT(WorkoutReaction.user_id) as reactions,
				EXISTS(SELECT 0 FROM workout_reactions WHERE user_id = :user_id AND workout_id = Workout.workout_id) AS reacted
				
			FROM followers AS Follower
			INNER JOIN `workouts` AS Workout
				ON Workout.user_id = Follower.user_id
			INNER JOIN users AS User
				ON Workout.user_id = User.user_id
			INNER JOIN gyms AS Gym
				ON Workout.gym_id = Gym.gym_id
			LEFT JOIN workout_reactions AS WorkoutReaction
				ON WorkoutReaction.workout_id = Workout.workout_id
			WHERE Follower.follower_id = :user_id
			$before_query
			
			GROUP BY Workout.workout_id
			ORDER BY Workout.workout_id DESC
		) AS newsfeed
		
		LEFT JOIN (
			SELECT 
				COUNT(comment_id) AS comments,
				MAX(comment_id) AS comment_id,
				workout_id
			FROM workout_comments

			GROUP BY workout_id
		) AS WorkoutCommentJoin
			ON WorkoutCommentJoin.workout_id = newsfeed.workout_id 
		
		LEFT JOIN workout_comments AS WorkoutComment
			ON WorkoutComment.comment_id = WorkoutCommentJoin.comment_id
		
		LEFT JOIN users AS User
			ON User.user_id = WorkoutComment.user_id

		UNION

		SELECT
		newsfeed.*,
		COALESCE(WorkoutCommentJoin.comments, 0) as comments,
		WorkoutComment.comment,
		WorkoutComment.created AS comment_created,
		User.name as comment_user_name, User.user_id AS comment_user_id, User.avatar AS comment_avatar
		

		FROM (
			SELECT 
				Workout.workout_id, Workout.title, Workout.date, Workout.duration, Workout.description, 
				User.name as user_name, User.user_id, User.avatar, 
				Gym.gym_id, Gym.name as gym_name,
				COUNT(WorkoutReaction.user_id) as reactions,
				EXISTS(SELECT 0 FROM workout_reactions WHERE user_id = :user_id AND workout_id = Workout.workout_id) AS reacted
				
			FROM `workouts` AS Workout
			INNER JOIN users AS User
				ON Workout.user_id = User.user_id
			INNER JOIN gyms AS Gym
				ON Workout.gym_id = Gym.gym_id
			LEFT JOIN workout_reactions AS WorkoutReaction
				ON WorkoutReaction.workout_id = Workout.workout_id
			WHERE Workout.user_id = :user_id
			$before_query

			GROUP BY Workout.workout_id
			ORDER BY Workout.workout_id DESC
		) AS newsfeed
		
		LEFT JOIN (
			SELECT 
				COUNT(comment_id) AS comments,
				MAX(comment_id) AS comment_id,
				workout_id
			FROM workout_comments

			GROUP BY workout_id
		) AS WorkoutCommentJoin
			ON WorkoutCommentJoin.workout_id = newsfeed.workout_id 
		
		LEFT JOIN workout_comments AS WorkoutComment
			ON WorkoutComment.comment_id = WorkoutCommentJoin.comment_id
		
		LEFT JOIN users AS User
			ON User.user_id = WorkoutComment.user_id

		ORDER BY workout_id DESC
		LIMIT 10
		")
		->setParameters($query_params)
		->execute($database)
		->getAll();

		$rows = static::addSummaryToNewsfeed($database, $rows);
		$rows = static::addPhotosToNewsfeed($database, $rows);
		$rows = static::addReactionsToNewsfeed($database, $rows);
		return $rows;
	}

	public static function getNewsfeedForUser($database, $user_id, $viewing_user_id, $parameters=[]) {
		$before_id = $parameters['before_id'] ?? '';
		$before_query = $before_id ? 'AND Workout.workout_id < :before_id' : '';

		$query_params = [":user_id" => $user_id, ":viewing_user_id" => $viewing_user_id];
		if ($before_id)
			$query_params[':before_id'] = $before_id;

		$rows = static::sql("
		SELECT
		newsfeed.*,
		COALESCE(WorkoutCommentJoin.comments, 0) as comments,
		WorkoutComment.comment,
		WorkoutComment.created AS comment_created,
		User.name as comment_user_name, User.user_id AS comment_user_id, User.avatar AS comment_avatar
		

		FROM (
			SELECT 
				Workout.workout_id, Workout.title, Workout.date,  Workout.duration, Workout.description,
				User.name as user_name, User.user_id, User.avatar, 
				Gym.gym_id, Gym.name as gym_name,
				COUNT(WorkoutReaction.user_id) as reactions,
				EXISTS(SELECT 0 FROM workout_reactions WHERE user_id = :user_id AND workout_id = Workout.workout_id) AS reacted
				
			FROM `workouts` AS Workout
			INNER JOIN users AS User
				ON Workout.user_id = User.user_id
			INNER JOIN gyms AS Gym
				ON Workout.gym_id = Gym.gym_id
			LEFT JOIN workout_reactions AS WorkoutReaction
				ON WorkoutReaction.workout_id = Workout.workout_id
			WHERE Workout.user_id = :user_id
			$before_query
			
			GROUP BY Workout.workout_id
			ORDER BY Workout.workout_id DESC
		) AS newsfeed
		
		LEFT JOIN (
			SELECT 
				COUNT(comment_id) AS comments,
				MAX(comment_id) AS comment_id,
				workout_id
			FROM workout_comments

			GROUP BY workout_id
		) AS WorkoutCommentJoin
			ON WorkoutCommentJoin.workout_id = newsfeed.workout_id 
		
		LEFT JOIN workout_comments AS WorkoutComment
			ON WorkoutComment.comment_id = WorkoutCommentJoin.comment_id
		
		LEFT JOIN users AS User
			ON User.user_id = WorkoutComment.user_id

		ORDER BY workout_id DESC
		LIMIT 10
				")
				->setParameters($query_params)
				->execute($database)
				->getAll();

        $rows = static::addSummaryToNewsfeed($database, $rows);
		$rows = static::addPhotosToNewsfeed($database, $rows);
		$rows = static::addReactionsToNewsfeed($database, $rows);
		return $rows;
	}

	public static function getNewsfeedForGym($database, $gym_id, $viewing_user_id, $parameters=[]) {
		$before_id = $parameters['before_id'] ?? '';
		$before_query = $before_id ? 'AND Workout.workout_id < :before_id' : '';

		$query_params = [":gym_id" => $gym_id, ":viewing_user_id" => $viewing_user_id];
		if ($before_id)
			$query_params[':before_id'] = $before_id;

		$rows = static::sql("
		SELECT
		newsfeed.*,
		COALESCE(WorkoutCommentJoin.comments, 0) as comments,
		WorkoutComment.comment,
		WorkoutComment.created AS comment_created,
		User.name as comment_user_name, User.user_id AS comment_user_id, User.avatar AS comment_avatar
		

		FROM (
			SELECT 
				Workout.workout_id, Workout.title, Workout.date, Workout.duration, Workout.description,
				User.name as user_name, User.user_id, User.avatar, 
				Gym.gym_id, Gym.name as gym_name,
				COUNT(WorkoutReaction.user_id) as reactions,
				EXISTS(SELECT 0 FROM workout_reactions WHERE user_id = :viewing_user_id AND workout_id = Workout.workout_id) AS reacted
				
			FROM `workouts` AS Workout
			INNER JOIN users AS User
				ON Workout.user_id = User.user_id
			INNER JOIN gyms AS Gym
				ON Workout.gym_id = Gym.gym_id
			LEFT JOIN workout_reactions AS WorkoutReaction
				ON WorkoutReaction.workout_id = Workout.workout_id
			WHERE Gym.gym_id = :gym_id
			$before_query
			
			GROUP BY Workout.workout_id
			ORDER BY Workout.workout_id DESC
		) AS newsfeed
		
		LEFT JOIN (
			SELECT 
				COUNT(comment_id) AS comments,
				MAX(comment_id) AS comment_id,
				workout_id
			FROM workout_comments

			GROUP BY workout_id
		) AS WorkoutCommentJoin
			ON WorkoutCommentJoin.workout_id = newsfeed.workout_id 
		
		LEFT JOIN workout_comments AS WorkoutComment
			ON WorkoutComment.comment_id = WorkoutCommentJoin.comment_id
		
		LEFT JOIN users AS User
			ON User.user_id = WorkoutComment.user_id

		ORDER BY workout_id DESC
		LIMIT 10
				")
				->setParameters($query_params)
				->execute($database)
				->getAll();

        $rows = static::addSummaryToNewsfeed($database, $rows);
		$rows = static::addPhotosToNewsfeed($database, $rows);
		$rows = static::addReactionsToNewsfeed($database, $rows);
		return $rows;
	}

	public static function getById($database, $id) {
		$row = static::select([
					static::class => ["*"],
					User::class => ["name as user_name", "user_id", "avatar"],
					Gym::class => ["gym_id", "name as gym_name"]
				])
				->from(static::class)
				->innerJoin(User::class, "user_id")
				->innerJoin(Gym::class, "gym_id")
				->orderBy("Workout.workout_id", "desc")
				->where("Workout.workout_id = :id")
				->setParameter(":id", $id)
				->execute($database)
				->getRow();

		return $row;
	}

	public function getExercises($database, $id) {
		$row = static::select([
					Exercise::class => "*",
					ExerciseType::class => "*"
				])
				->from(static::class)
				->innerJoin(Exercise::class, "workout_id")
				->innerJoin(ExerciseType::class, "type_id", Exercise::class)
				->where("Workout.workout_id = :id")
				->setParameter(":id", $id)
				->execute($database)
				->getAll();

		return $row;
	}

	public static function getComments($database, $id) {
		$row = static::select([
					WorkoutComment::class => ["comment_id", "comment", "created"],
					User::class => ["name as user_name", "user_id", "avatar"],
				])
				->from(WorkoutComment::class)
				->innerJoin(User::class, "user_id")
				->where("WorkoutComment.workout_id = :id")
				->setParameter(":id", $id)
				->execute($database)
				->getAll();

		return $row;
	}

	public static function getReactions($database, $workout_id, $user_id) {
		$row = static::select([
					"COUNT(WorkoutReaction.user_id) as count",
					"EXISTS(SELECT 0 FROM workout_reactions WHERE user_id = :user_id AND workout_id = :id) AS reacted"
				])
				->from(WorkoutReaction::class)
				->where("WorkoutReaction.workout_id = :id")
				->groupBy("WorkoutReaction.workout_id")
				->setParameter(":id", $workout_id)
				->setParameter(":user_id", $user_id)
				->execute($database)
				->getRow();

		return $row;
	}
}
?>