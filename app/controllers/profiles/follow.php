<?php
namespace Web\Pages;
use \Model\User;

class Page extends \PageBuilder {
	protected function init() {}

	protected function content() {
		$id = $this->data->path->profile;
		try {
			User::follow($this->database, $this->account->user_id, $id);
		} catch (\Exception $e) {}

		$this->redirect("profile/$id");
	}
}

new Page();
?>