<?php
namespace Web\Pages;
use \Model\User;
use \Model\Workout;
use \Model\Exercise;

class Page extends \PageBuilder {
	protected function init() {
		$this->metadata->setTitle("Dodaj trening");
		$this->addActions([
			"add" => "onAdd"
		]);
		$this->metadata->addScript("add_workout.js", true, true);
	}

	protected function content() {
		$exercises = \Model\ExerciseCategory::getTree($this->database);
		$this->response->addTemplate("workout/add.php", []);
	}

	protected function onAdd() {
		$workout = new Workout();
		$workout->gym_id = $_POST["gym_id"] ?? "";
		$workout->user_id = $this->account->user_id;
		$workout->name = $_POST["name"] ?? "";
		$workout->date = date("Y-m-d h:i:s", time());
		$success = $workout->save($this->database);

		$exercise = new Exercise();
		$exercise->workout_id = $workout->workout_id;
		$exercise->type_id = $_POST["type_id"] ?? "";
		$exercise->reps = $_POST["reps"] ?? "";
		$exercise->weight = $_POST["weight"] ?? "";
		$exercise->failure = $_POST["failure"] ?? "";
		$success = $success and $exercise->save($this->database);

		if ($success) {
			$this->redirect("workout/".$workout->workout_id);
		} else {
			$this->snackbar->set(400, "Nie udało się dodać treningu.");
		}

	}
}

new Page();
?>