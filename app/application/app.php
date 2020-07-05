<?php
include_once __DIR__ . "/framework/include.php";

foreach (glob(ROOT_PATH . "/models/*.php") as $filename) {
    include_once $filename;
}


class PageBuilder extends \WebBuilder {
    protected $account;
    protected $require_auth = true;

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
		
		$this->metadata->addScript("moment-with-locales.min.js");
		$this->metadata->addScript("api.js");

		$this->metadata->addStylesheet("elements.css");
		$this->metadata->addStylesheet("style.css");
	}

	public function run() {
		if ($this->account->isLoggedIn() || !$this->require_auth) {
			parent::run();
		} else {
			$this->authRedirect();
		}	
	}

	protected function authRedirect() {
		$this->redirect("");
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

	protected function footer($metadata) {
		$this->response->addTemplate("skeleton/footer.php", $metadata);
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

	public function run() {
		$method = $_SERVER['REQUEST_METHOD'];
		if ($method == "GET")
			return $this->get();
		elseif ($method == "POST")
			return $this->post();
		elseif ($method == "PUT")
			return $this->put();
		elseif ($method == "DELETE")
			return $this->delete();

		return ""; // unknown method
	}

	// virtual methods to override:
	public function post() {return "";}
	public function get() {return "";}
	public function put() {return "";}
	public function delete() {return "";}

	public function __destruct() {
		echo $this->run();
	}
}
?>