<?php
namespace Web\Pages;
use \Model\Category;

class Index extends \PageBuilder {
	protected function init() {
		$this->metadata->setTitle("Nie znaleziono strony");
	}

	protected function content() {
		$this->response->addTemplate("codes/404.php", []);
	}
}

new Index();
?>