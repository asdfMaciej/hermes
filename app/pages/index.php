<?php
namespace Web\Pages;
use \Model\User;
use \Model\Workout;

class Index extends \PageBuilder {
	protected function init() {
		$this->metadata->setTitle("Index");
	}

	protected function content() {
		if ($this->account->isLoggedIn()) {
			$workouts = Workout::getNewsfeedList($this->database);
			$this->response->addTemplate("newsfeed/index.php", ["workouts" => $workouts]);
		} else {
			$this->response->addTemplate("static/index.php", []);
		}
	}
}

new Index();
?>