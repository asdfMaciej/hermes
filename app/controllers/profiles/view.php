<?php
namespace Web\Pages;
use \Model\User;
use \Model\Workout;
use \Model\Exercise;
use \Model\Gym;

class Page extends \PageBuilder {
	protected function init() {
		$this->metadata->addScript("reaction-button.js", true, true);
        $this->metadata->addScript("profile-list.js", true, true);
		$this->metadata->addScript("newsfeed.js", true, true);
		$this->metadata->addScript("profile.js", true, true);
	}

	protected function content() {
		$id = $this->data->path->profile;
		$user = User::getProfileById($this->database, $id, $this->account->user_id);
		if (!$user)
			return $this->response->addTemplate("codes/404.php");

		$gym_frequency = User::getGymFrequency($this->database, $id);

		$gym_timeseries = [];
		foreach ($gym_frequency as $row) {
		    $gym_timeseries[$row["date"]] = $row["count"];
        }
        $this->metadata->setTitle($user["name"]);
		$this->response->addTemplate("profile/view.php", [
			"user" => $user,
			"account" => $this->account,
            "gym_timeseries" => $gym_timeseries
		]);
	}
}

new Page();
?>