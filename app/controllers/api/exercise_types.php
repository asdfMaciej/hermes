<?php
namespace Web\Pages;
use \Model\ExerciseType;

class Page extends \APIBuilder {
    public function get() {
        return $this->generateAndSet(["exercise_types" => ExerciseType::getExerciseTypes($this->database)], 200);
    }
}

new Page();