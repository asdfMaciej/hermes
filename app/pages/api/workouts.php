<?php
namespace Web\Pages;
use \Model\Workout;
use \Model\Exercise;

class Page extends \APIBuilder {
	public function post() {
		$workout = $this->data->json["workout"] ?? [];
		$workout["user_id"] = $this->account->user_id;

		$exercises = $this->data->json["exercises"] ?? [];
		
		$this->database->beginTransaction();
		try {
			Workout::fromArray($workout)->save($this->database);
			$workout_id = $this->database->lastInsertId();

			foreach ($exercises as $exercise) {
				$exercise["workout_id"] = $workout_id;
				Exercise::fromArray($exercise)->save($this->database);
			}
		} catch (\Exception $e) {
			$this->database->rollBack();
			return $this->generateAndSet([], 400);
		}
		$this->database->commit();
		
		return $this->generateAndSet(["workout_id" => $workout_id], 201);
	}
}

new Page();
?>