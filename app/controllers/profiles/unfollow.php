<?php
namespace Web\Pages;
use \Model\User;

class Page extends \PageBuilder {
	protected function init() {
		$this->metadata->setTitle("Index");
		$this->addActions([]);
	}

	protected function content() {
		$id = $this->data->path->profile;
		try {
			User::unfollow($this->database, $this->account->user_id, $id);
		} catch (\Exception $e) {}

		$this->redirect("profile/$id");
	}
}

new Page();
?>