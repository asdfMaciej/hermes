<?php
namespace Web\Pages;
use \Model\User;
use \Model\Workout;
use \Model\Exercise;

class Page extends \PageBuilder {
	protected function init() {
		$this->metadata->setTitle("Dodaj trening");
		$this->metadata->addScript("add_workout.js", true, true);
		$this->metadata->addScript("exercise-charts.js");
	}

	protected function content() {
		$this->response->addTemplate("workout/add.php", []);
	}
}

new Page();
?>