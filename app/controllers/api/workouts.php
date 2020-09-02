<?php
namespace Web\Pages;
use \Model\Workout;
use \Model\Exercise;
use mysql_xdevapi\Exception;

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
			    // comma separator doesn't work
			    $exercise["weight"] = str_replace(",", ".", $exercise["weight"]);

			    // for the moment, leave it as a client-side feature due to UI fail
			    $exercise["failure"] = 0;

			    if ($exercise["show_reps"] && intval($exercise["reps"]) <= 0)
			        throw new Exception("Reps <= 0");

			    if ($exercise["show_duration"] && intval($exercise["duration"]) <= 0)
			        throw new Exception("Duration <= 0");

			    if ($exercise["show_weight"] && floatval($exercise["weight"]) < 0)
			        throw new Exception("Weight < 0");

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