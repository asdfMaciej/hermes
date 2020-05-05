<?php
namespace Web\Pages;
use \Model\User;
use \Model\Workout;
use \Model\Photo;
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

		$records = Gym::getExerciseRecords($this->database, $id);
		$frequenters = Gym::getFrequenters($this->database, $id);
		$album = Photo::getForAlbumId($this->database, $gym["album_id"]);
		$this->response->addTemplate("gym/view.php", [
			"gym" => $gym,
			"album" => $album,
			"records" => $records,
			"frequenters" => $frequenters
		]);

		$workouts = Workout::getNewsfeedForGym($this->database, $id);
		$this->response->addTemplate("newsfeed/newsfeed.php", [
			"workouts" => $workouts
		]);
	}
}

new Page();
?>