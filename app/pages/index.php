<?php
namespace Web\Pages;
use \Model\User;
use \Model\Workout;

class Index extends \PageBuilder {
	protected $require_auth = false;

	protected function init() {
		$this->metadata->setTitle("Index");
	}

	protected function content() {
		if ($this->account->isLoggedIn()) {
			$workouts = Workout::getNewsfeedList($this->database);
			$statistics = User::getStatistics($this->database, $this->account->user_id);

			$this->response->addTemplate("newsfeed/index.php", [
				"workouts" => $workouts, 
				"account" => $this->account,
				"statistics" => $statistics
			]);
		} else {
			$this->response->addTemplate("static/index.php", []);
		}
	}
}

new Index();
?>