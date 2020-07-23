<?php
namespace Web\Pages;
use \Model\User;

class Page extends \PageBuilder {
	protected function init() {
		$this->metadata->setTitle("Ustawienia konta");
		$this->addActions([
			"change" => "onChange"
		]);
	}

	protected function content() {
		$user = User::getProfileById($this->database, $this->account->user_id, $this->account->user_id);
		if (!$user)
			return $this->response->addTemplate("codes/404.php");

		$this->response->addTemplate("profile/settings.php", [
			"user" => $user,
			"account" => $this->account
		]);
	}

	protected function onChange() {
		$name = $_POST["name"] ?? "";
		if (!$name)
			return;

		$user = User::getSingleItem($this->database, ["user_id" => $this->account->user_id]);
		$user->name = $name; 
		if ($user->save($this->database)) {
            $this->redirect("settings");
		} else {
            $this->redirect("settings");
		}
	}
}

new Page();

// todo: each db orm model could implement validation checks before saving
?>