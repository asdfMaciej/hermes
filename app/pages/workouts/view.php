<?php
namespace Web\Pages;
use \Model\User;
use \Model\Workout;
use \Model\Exercise;
use \Model\Gym;

class Page extends \PageBuilder {
	protected function init() {
		$this->metadata->setTitle("Index");
		$this->addActions([]);
	}

	protected function content() {
		$id = $this->data->path->workout;
		$workout = Workout::getById($this->database, $id);
		if (!$workout)
			return $this->response->addTemplate("codes/404.php");

		$exercises = Workout::getExercises($this->database, $id);
		$gym_id = $workout["gym_id"];
		$gym = Gym::getSingleItem($this->database, ["gym_id" => $gym_id]);

		$this->response->addTemplate("workout/view.php", [
			"workout" => $workout,
			"gym" => $gym,
			"exercises" => $exercises
		]);
	}
}

new Page();
?>