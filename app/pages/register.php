<?php
namespace Web\Pages;
use \Model\User;

class Index extends \PageBuilder {
	protected $require_auth = false;

	protected function init() {
		$this->metadata->setTitle("Index");
		$this->addActions([
			"register" => "onRegister"
		]);
	}

	protected function content() {
		$this->response->addTemplate("forms/register.php", []);
	}

	protected function onRegister() {
		$login = $_POST["login"];
		$password = $_POST["password"];
		$name = $_POST["name"];
		
		if (!$this->verifyCredentials($login, $password, $name))
			return $this->snackbar->set(400, "Nie spełniono wymagań dotyczących danych!");

		$user = new User();
		$user->login = $login;
		$user->password = $password;
		$user->name = $name;

		if ($user->register($this->database)) {
			return $this->redirect("login");
		} else {
			return $this->snackbar->set(400, "Nie udało się zarejestrować.");
		}
	}

	protected function verifyCredentials($login, $password, $name) {
		$valid = true;
		$valid = $valid && preg_match("~[^a-zA-Z0-9\-\_\.]~iU", $login) ? false : true;
		$valid = $valid && strlen($login) >= 4;
		$valid = $valid && strlen($password) >= 8;
		$valid = $valid && strlen($name) >= 6;
		return $valid;
	}
}

new Index();
?>