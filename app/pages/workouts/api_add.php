<?php
namespace Web\Pages;
use \Model\User;
use \Model\Workout;
use \Model\Exercise;

class Page extends \APIBuilder {
	public function run() {
		//var_dump($this->account->user_id);
		//var_dump($this->account->name);
		return $this->generateAndSet(["name" => $this->account->name]);
	}
}

new Page();
?>