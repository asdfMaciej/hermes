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

		$this->metadata->addScript("reaction-button.js", true, true);
		$this->metadata->addScript("newsfeed.js", true, true);
	}

	protected function content() {
		$id = $this->data->path->profile;
		$user = User::getProfileById($this->database, $id, $this->account->user_id);
		if (!$user)
			return $this->response->addTemplate("codes/404.php");

		$this->response->addTemplate("profile/view.php", [
			"user" => $user,
			"account" => $this->account
		]);

		$workouts = Workout::getNewsfeedForUser($this->database, $id, $this->account->user_id);
		$this->response->addTemplate("newsfeed/newsfeed.php", [
			"workouts" => $workouts
		]);
	}
}

new Page();
?>