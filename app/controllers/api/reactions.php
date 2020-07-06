<?php
namespace Web\Pages;
use \Model\WorkoutReaction;

class Page extends \APIBuilder {
	public function post() {
		$workout_id = $this->data->json["workout_id"] ?? 0;
		$action = $this->data->json["action"] ?? "";
		$user_id = $this->account->user_id;

		if (!$workout_id || !($action == "react" || $action == "unreact")) {
			return $this->generateAndSet([], 400);
		}
		
		try {
			if ($action == "react") {
				WorkoutReaction::react($this->database, $user_id, $workout_id);
			} elseif ($action == "unreact") {
				WorkoutReaction::unreact($this->database, $user_id, $workout_id);
			}
		} catch (\Exception $e) {
			return $this->generateAndSet([], 400);
		}
		
		return $this->generateAndSet([], 200);
	}
}

new Page();
?>