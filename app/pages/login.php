<?php
namespace Web\Pages;
use \Model\User;

class Page extends \PageBuilder {
	protected function init() {
		$this->metadata->setTitle("Index");
		$this->addActions([
			"login" => "onLogin",
			"logout" => "onLogout"
		]);
	}

	protected function content() {
		$this->response->addTemplate("forms/login.php", []);
	}

	protected function onLogin() {
		$login = $_POST["login"];
		$password = $_POST["password"];
		if ($this->login($login, $password)) {
			$this->redirect("");
		} else {
			$this->snackbar->set(400, "Nie udało się zalogować.");
		}
	}

	protected function onLogout() {
		if ($this->logout())
			$this->redirect("");
		else
			$this->snackbar->set(400, "Nie udało się wylogować.");
	}
}

new Page();
?>