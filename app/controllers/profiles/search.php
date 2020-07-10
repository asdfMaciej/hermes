<?php
namespace Web\Pages;
use \Model\User;

class Page extends \PageBuilder {
	protected function init() {
		$this->metadata->setTitle("Index");
	}

	protected function content() {
		$q = str_replace("%", "", $_GET["q"] ?? "");
		$query = "%" . $q . "%";
		$users = User::searchProfiles($this->database, $query, $this->account->user_id);

		$this->response->addTemplate("profile/search.php", [
			"users" => $users,
			"account" => $this->account,
			"query" => $q
		]);
	}
}

new Page();

?>