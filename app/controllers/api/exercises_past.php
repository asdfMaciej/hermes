<?php
namespace Web\Pages;
use \Model\Exercise;
use \Model\ExerciseType;

class Page extends \APIBuilder {
    public function get() {
        $user_id = $this->account->user_id;
        $type_id = $this->data->get["type_id"] ?? "";
        $exercises = Exercise::getPastExercises($this->database, $user_id, $type_id);
        $history = ExerciseType::getUserExerciseHistory($this->database, $type_id, $user_id);
        return $this->generateAndSet(compact("exercises", "history"), 200);
    }
}

new Page();