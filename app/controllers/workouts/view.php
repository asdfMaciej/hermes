<?php
namespace Web\Pages;
use \Model\User;
use \Model\Workout;
use \Model\Exercise;
use \Model\Gym;
use \Model\Photo;
use \Model\WorkoutComment;

class Page extends \PageBuilder {
	protected function init() {
		$this->metadata->setTitle("Index");
		$this->addActions([
			"comment" => "onComment",
		]);

		$this->metadata->addScript("reaction-button.js", true, true);
		$this->metadata->addScript("view-workout.js", true, true);
	}

	protected function onComment() {
		$comment = new WorkoutComment();
		$comment->workout_id = $this->data->path->workout;
		$comment->user_id = $this->account->user_id;
		$comment->comment = $_POST["comment"] ?? "";
		$success = $comment->save($this->database);

		if ($success) {
			$this->redirect("workout/".$this->data->path->workout);
		} else {
			$this->snackbar->set(400, "Nie udało się dodać treningu.");
		}
	}

	protected function content() {
		$id = $this->data->path->workout;
		$workout = Workout::getById($this->database, $id);
		if (!$workout)
			return $this->response->addTemplate("codes/404.php");

		$exercises = Workout::getExercises($this->database, $id);
		$gym_id = $workout["gym_id"];
		$gym = Gym::getSingleItem($this->database, ["gym_id" => $gym_id]);
		$gym_album = Photo::getForAlbumId($this->database, $gym["album_id"]);

		$comments = Workout::getComments($this->database, $id);
		$reactions = Workout::getReactions($this->database, $id, $this->account->user_id);
		$this->response->addTemplate("workout/view.php", [
			"workout" => $workout,
			"gym" => $gym,
			"exercises" => $exercises,
			"gym_album" => $gym_album,
			"comments" => $comments,
			"reactions" => $reactions
		]);
	}
}

new Page();
?>