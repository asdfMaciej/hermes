<?php
namespace Web\Pages;
use \Model\User;
use \Model\Routine;

class Page extends \PageBuilder {
	protected function init() {
		$this->addActions([]);
	}

	protected function content() {
		$routines = Routine::getRoutines($this->database);
        $this->metadata->setTitle("Schematy treningowe");
		$this->response->addTemplate("routine/view.php", [
			"routines" => $routines
		]);
	}
}

new Page();
?>