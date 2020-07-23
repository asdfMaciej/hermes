<?php
namespace Web\Pages;
use \Model\User;
use \Model\Workout;
use \Model\ExerciseCategory;
use \Model\Gym;

class Page extends \PageBuilder {
	protected function init() {
		$this->metadata->setTitle("Lista ćwiczeń");
	}

	protected function content() {
		$exercises = ExerciseCategory::getTree($this->database);

		$this->response->addTemplate("exercises/view_all.php", [
			"exercises" => $exercises
		]);
	}
}

new Page();
?>