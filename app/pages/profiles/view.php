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
		$id = $this->data->path->profile;
		$user = User::getProfileById($this->database, $id);
		if (!$user)
			return $this->response->addTemplate("codes/404.php");

		$this->response->addTemplate("profile/view.php", [
			"user" => $user,
			"account" => $this->account
		]);
	}
}

new Page();
?>