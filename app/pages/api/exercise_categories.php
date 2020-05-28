<?php
namespace Web\Pages;
use \Model\ExerciseCategory;

class Page extends \APIBuilder {
	public function get() {
		return $this->generateAndSet(["exercise_categories" => ExerciseCategory::getTree($this->database)], 200);
	}
}

new Page();
?>