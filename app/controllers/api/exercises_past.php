<?php
namespace Web\Pages;
use \Model\Exercise;

class Page extends \APIBuilder {
    public function get() {
        $user_id = $this->account->user_id;
        $type_id = $this->data->get["type_id"] ?? "";
        return $this->generateAndSet(["exercises" => Exercise::getPastExercises($this->database, $user_id, $type_id)], 200);
    }
}

new Page();