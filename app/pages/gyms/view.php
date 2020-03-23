<?php
namespace Web\Pages;
use \Model\User;
use \Model\Workout;
use \Model\Exercise;
use \Model\Gym;

class Page extends \PageBuilder {
	protected function init() {
		$this->metadata->setTitle("Index");
		$this->addActions([]);
	}

	protected function content() {
		$id = $this->data->path->gym;
		$gym = Gym::getSingleItem($this->database, ["gym_id" => $id]);
		
		if (!$gym)
			return $this->response->addTemplate("codes/404.php");

		$this->response->addTemplate("gym/view.php", [
			"gym" => $gym
		]);
	}
}

new Page();
?>