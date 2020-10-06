<?php
namespace Web\Pages;
use \Model\User;
use \Model\Workout;

class Index extends \PageBuilder {
	protected $require_auth = false;

	protected function init() {
		$this->addActions([
			"login" => "onLogin",
			"logout" => "onLogout",
			"register" => "onRegister"
		]);

		$this->metadata->addScript("reaction-button.js", true, true);
		$this->metadata->addScript("newsfeed.js", true, true);
	}

	protected function content() {
		if ($this->account->isLoggedIn()) {
            $this->metadata->setTitle("Aktualności");
			$statistics = User::getStatistics($this->database, $this->account->user_id);

			$this->response->addTemplate("newsfeed/index.php", [
				"account" => $this->account,
				"statistics" => $statistics
			]);
		} else {
            $this->metadata->setTitle("Trenuj ze znajomymi");
			$this->response->addTemplate("static/index.php", []);
		}
	}

	protected function onRegister() {
		$login = $_POST["login"];
		$password = $_POST["password"];
		$name = $_POST["name"];
		
		if (!$this->verifyCredentials($login, $password, $name))
			return $this->snackbar->set(400, "Nie spełniono wymagań: hasło >= 8 znaków, login alfanumeryczny, dopuszczalne znaki specjalne: - _ .");

		$user = new User();
		$user->login = $login;
		$user->password = $password;
		$user->name = $name;

		if ($user->register($this->database)) {
			$this->login($login, $password);
			return $this->snackbar->set(200, "Rejestracja udana!");
		} else {
			return $this->snackbar->set(400, "Nie udało się zarejestrować.");
		}
	}

	protected function verifyCredentials($login, $password, $name) {
		$valid = true;
		$valid = $valid && preg_match("~[^a-zA-ZżźćńółęąśŻŹĆĄŚĘŁÓŃ0-9\-\_\.]~iU", $login) ? false : true;
		$valid = $valid && strlen($login) >= 4;
		$valid = $valid && strlen($password) >= 8;
		$valid = $valid && strlen($name) >= 6;
		return $valid;
	}

	protected function onLogin() {
		$login = $_POST["login"];
		$password = $_POST["password"];
		if ($this->login($login, $password)) {
			$this->redirect("");
		} else {
			$this->snackbar->set(400, "Nie udało się zalogować.");
			$this->redirect("");
		}
	}

	protected function onLogout() {
		if ($this->logout()) {
			$this->redirect("");
		} else {
			$this->snackbar->set(400, "Nie udało się wylogować.");
			$this->redirect("");
		}
	}
}

new Index();
?>