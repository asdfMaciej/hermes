<?php
namespace Web\Pages;
use \Model\User;
use \Model\Workout;
use \Model\Exercise;

class Page extends \PageBuilder {
	protected function init() {
		$this->metadata->setTitle("Index");
		$this->addActions([]);
	}

	protected function content() {
		$this->response->addTemplate("workout/view.php", []);
	}
}

new Page();
?>