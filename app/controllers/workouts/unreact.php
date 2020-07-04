<?php
namespace Web\Pages;
use \Model\User;
use \Model\WorkoutReaction;

class Page extends \PageBuilder {
	protected function init() {
		$this->metadata->setTitle("Index");
		$this->addActions([]);
	}

	protected function content() {
		$id = $this->data->path->workout;
		try {
			WorkoutReaction::unreact($this->database, $this->account->user_id, $id);
		} catch (\Exception $e) {}

		$this->redirect("workout/$id");
	}
}

new Page();
?>