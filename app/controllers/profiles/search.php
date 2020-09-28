<?php
namespace Web\Pages;
use \Model\User;

class Page extends \PageBuilder {
	protected function init() {
		$this->metadata->addScript("profile-list.js", true, true);
		$this->metadata->addScript("search-users.js", true, true);
	}

	protected function content() {
	    $searched_string = $_GET["q"] ?? "";
        $this->metadata->setTitle("Wyszukiwarka - $searched_string");

		$q = str_replace("%", "", $searched_string);
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