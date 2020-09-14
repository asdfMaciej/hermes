<?php
namespace Model;
class Workout extends \DBModel {
	protected static $table_name = "workouts";
	protected static $primary_key = "workout_id";

	public $workout_id;
	public $user_id;
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

	protected static function addSummaryToNewsfeed($database, $rows) {
        $workout_ids = [];
        $query_parameters = [];
        $workout_stats = [];
        foreach ($rows as $row) {
            $workout_ids[] = $row["workout_id"];
            $workout_stats[$row["workout_id"]] = [];
            $query_parameters[] = "?";
        }
        if ($query_parameters) {
            $query_parameters = 'WHERE e.workout_id IN (' . implode(",", $query_parameters) . ')';
        } else {
            $query_parameters = "";
        }


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

	public static function getNewsfeedList($database, $user_id) {
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
		")
		->setParameter(":user_id", $user_id)
		->execute($database)
		->getAll();

		return static::addSummaryToNewsfeed($database, $rows);
	}

	public static function getNewsfeedForUser($database, $user_id, $viewing_user_id) {
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
				")
				->setParameter(":user_id", $user_id)
				->setParameter(":viewing_user_id", $viewing_user_id)
				->execute($database)
				->getAll();

        return static::addSummaryToNewsfeed($database, $rows);
	}

	public static function getNewsfeedForGym($database, $gym_id, $viewing_user_id) {
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
				")
				->setParameter(":gym_id", $gym_id)
				->setParameter(":viewing_user_id", $viewing_user_id)
				->execute($database)
				->getAll();

        return static::addSummaryToNewsfeed($database, $rows);
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