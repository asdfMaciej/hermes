<?php
namespace Web\Pages;
use \Model\User;

class Index extends \PageBuilder {
	protected function init() {
		$this->metadata->setTitle("Index");
	}

	protected function content() {
		if ($this->account->isLoggedIn()) {
			$this->response->addTemplate("newsfeed/index.php", []);
		} else {
			$this->response->addTemplate("static/index.php", []);
		}
	}
}

new Index();
?>