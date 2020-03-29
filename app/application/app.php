<?php
include_once ROOT_PATH . "/framework/include.php";

foreach (glob(__DIR__ . "/models/*.php") as $filename) {
    include_once $filename;
}

class PageBuilder extends \WebBuilder {
    protected $account;

	public function __construct() {
		parent::__construct();
		$this->account = new \Model\UserSession();
		if (DEBUG) {
			$this->metadata->addScript("vue.js");
			$this->metadata->addScript("axios.min.js");
		} else {
			$this->metadata->addScript("https://vuejs.org/js/vue.min.js", false);
			$this->metadata->addScript("https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js", false);
		}
		
		$this->metadata->addStylesheet("style.css");
		
	}

	public function login($login, $password) {
		$user = \Model\User::login($this->database, $login, $password);
		if (!$user) {
			return false;
		}
		
		$this->account->loginUser($user);
		return true;
	}

	public function logout() {
		$this->account->logout();
		return true;
	}

	protected function header($metadata) {
		$metadata["account"] = $this->account;
		$this->response->addTemplate("skeleton/header.php", $metadata);
	}

	protected function footer() {
		$this->response->addTemplate("skeleton/footer.php");
	}

	protected function showSnackbar($message, $code) {
		$this->response->addTemplate("skeleton/snackbar.php", [
			"message" => $message,
			"code" => $code,
		]);
	}
}

class APIBuilder extends \JSONBuilder {
	protected $account;

	public function __construct() {
		parent::__construct();
		$this->account = new \Model\UserSession();
	}

	public function run() {return "";} // virtual, to override

	public function __destruct() {
		echo $this->run();
	}
}
?>