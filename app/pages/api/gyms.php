<?php
namespace Web\Pages;
use \Model\Gym;

class Page extends \APIBuilder {
	public function get() {
		return $this->generateAndSet(["gyms" => Gym::getItems($this->database)], 200);
	}
}

new Page();
?>