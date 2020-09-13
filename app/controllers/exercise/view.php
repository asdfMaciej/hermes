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
		$this->response->addTemplate("exercise/view.php", [
			"exercise" => $exercise,
            "weight_records" => $weight_records
		]);
	}
}

new Page();
?>