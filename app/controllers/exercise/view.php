<?php
namespace Web\Pages;
use \Model\ExerciseType;

class Page extends \PageBuilder {
	protected function init() {
		$this->metadata->setTitle("Lista ćwiczeń");
	}

	protected function content() {
	    $id = $this->data->path->exercise;
	    $exercise = ExerciseType::getSingleItem($this->database, ["type_id" => $id]);

        if (!$exercise)
            return $this->response->addTemplate("codes/404.php");

        $weight_records = ExerciseType::getWeightRecords($this->database, $id);
        $user_history = ExerciseType::getUserExerciseHistory($this->database, $id, $this->account->user_id);
		$this->response->addTemplate("exercise/view.php", [
			"exercise" => $exercise,
            "weight_records" => $weight_records,
            "user_history" => $user_history
		]);
	}
}

new Page();
?>