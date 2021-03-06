<?php
class JSONBuilder {
	private $response_code = 200;
	protected $database;
	protected $database_class;
	protected $data;

	public function __construct() {
		$this->database_class = new DBClass();
		$this->database = $this->database_class->getConnection();
		$this->data = new DataHandler();
	}
	
	public function r_ok() {$this->response_code = 200;}
	public function r_created() {$this->response_code = 201;}
	public function r_bad_request() {$this->response_code = 400;}
	public function r_unauthorized() {$this->response_code = 401;}
	public function r_forbidden() {$this->response_code = 403;}
	public function r_not_found() {$this->response_code = 404;}

	public function generateAndSet($data=[], $code=200) {
		$this->response_code = $code;
		return $this->generate($data);
	}

	public function generate($data=[]) {
		if (DEBUG) {
			// Let's make it debug only for now
			header("Access-Control-Allow-Origin: *");
		}

		header("Content-Type: application/json");
		http_response_code($this->response_code);
		return $this->json($data);
	}

	protected function json($str) {
		return json_encode($str, JSON_UNESCAPED_SLASHES);
	}
}

class WebBuilder {
	protected $config;
	protected $metadata;
	protected $template_path;
	protected $database;
	protected $database_class;
	protected $response;
	protected $data;
	protected $snackbar;

	protected $functions_map = [];

	public function __construct() {
		$this->database_class = new DBClass();
		$this->database = $this->database_class->getConnection();
		$this->response = new TemplateBuilder();
		$this->metadata = new PageMetadata();
		$this->config = new WebBuilderConfig();
		$this->data = new DataHandler();
		$this->snackbar = new PageSnackbar();
	}

	public function __destruct() {
		if ($this->config->autorun) {
			$this->run();
		}
	}

	/* virtual methods: */
	protected function header($metadata) {}
	protected function footer($metadata) {}
	protected function showSnackbar($message, $code) {}
	protected function content() {}
	protected function init() {}

	public function run() {
		$this->init();
		$this->handleActions();

		if ($this->snackbar->getMessage()) {
			$message = $this->snackbar->getMessage();
			$code = $this->snackbar->getCode();
			$this->showSnackbar($message, $code);
		}

		$this->content();
        $metadata = $this->metadata->getMetadata();

        $this->header($metadata);
		$this->footer($metadata);

		$this->render();
	}

	protected function render() {
		$this->response->generate();
	}

	protected function addAction($action, $function_name) {
		$this->functions_map[$action] = $function_name;
	}

	protected function addActions($actions) {
		foreach ($actions as $action => $function_name) {
			$this->addAction($action, $function_name);
		}
	}

	protected function handleActions() {
		$action_method = strtolower($this->config->action_method);
		$action_key = $this->config->action_key;

		if ($action_method == "get") {
			$value_array = $_GET;
		} elseif ($action_method == "post") {
			$value_array = $_POST;
		} else {
			throw new Exception("Invalid action retrieve method [post/get]: ".$action_method);
		}

		$action = $value_array[$action_key] ?? "";
		if (!array_key_exists($action, $this->functions_map)) {
			return False;
		}

		$function_name = $this->functions_map[$action];
		$call = [$this, $function_name]; // class object, function name
		$response = call_user_func($call, $this); // $this is passed as a param
		if (is_array($response)) {
			$this->snackbar->setCode($response[0]);
			$this->snackbar->setMessage($response[1]);
		}
		return True;
	}

	protected function redirect($path=null, $prevent_form_resubmission=true) {
		if (is_null($path))
			throw new Exception("Unspecified path for a redirect.");

		if ($prevent_form_resubmission) {
            header("HTTP/1.1 303 See Other");
        }
		header('Location: '.PATH_PREFIX."/".$path);
		exit();
	}

}

class PageMetadata {
	private $stylesheet_prefix = PATH_PREFIX . "/static/css/";
	private $script_prefix = PATH_PREFIX . "/static/js/";

	protected $title = "";
	protected $stylesheets = [];
	protected $scripts = [];
	protected $scripts_on_end = [];

	public function __construct() {}

	protected function addPostfix(&$name, $postfix) {
		$postfix_length = strlen($postfix);
		$postfix_index = 0 - $postfix_length;

		$name_postfix = substr($name, $postfix_index);
		if ($name_postfix !== $postfix) {
			$name .= $postfix;
		}
		return $name;
	}

	public function addStylesheet($name, $add_prefix=True) {
		$path = $add_prefix ? $this->stylesheet_prefix : "";
		$name = $path . $this->addPostfix($name, ".css");
		$this->stylesheets[] = $name;
	}

	public function addScript($name, $add_prefix=True, $on_end=False) {
		$path = $add_prefix ? $this->script_prefix : "";
		$name = $path . $this->addPostfix($name, ".js");
		if ($on_end) {
			$this->scripts_on_end[] = $name;
		} else {
			$this->scripts[] = $name;
		}
	}

	public function setTitle($title) {
		$this->title = $title;
	}

	public function getStylesheets() { return $this->stylesheets; }
	public function getScripts() { return $this->scripts; }
	public function getScriptsOnEnd() { return $this->scripts_on_end; }
	public function getTitle() { return $this->title; }

	public function getMetadata() {
		$metadata = [
			"stylesheets" => $this->getStylesheets(),
			"scripts" => $this->getScripts(),
			"scripts_on_end" => $this->getScriptsOnEnd(),
			"title" => $this->getTitle()
		];
		return $metadata; 
	}
}

class WebBuilderConfig {
	public $autorun = True;
	public $action_key = "action";
	public $action_method = "post";
}

class PageSnackbar {
	private $message = "";
	private $code = 200;

	public function setMessage($msg) {
		$this->message = $msg;
	}
	
	public function setCode($code) {
		$this->code = $code;
	}

	public function set($code, $message) {
		$this->message = $message;
		$this->code = $code;
	}

	public function getMessage() { return $this->message; }
	public function getCode() { return $this->code; }
}

class DataFromArray implements ArrayAccess {
	private $array;
	public function __construct(&$array) {
		$this->array = &$array;
	}

	public function &__get($key) {
		$value = $this->array[$key] ?? null;
		return $value;
	}

	public function __set($key, $value) {
		$this->array[$key] = $value;
	}

	/*
	 * ArrayAccess implementation (offset methods): 
	 * Enables accessing the model as an Array: 
	*/
	public function offsetSet($offset, $value) {
		$this->array[$offset] = $value;
	}

	public function offsetExists($offset) {
		return isset($this->array[$offset]); 
	}

	public function offsetUnset($offset) {
		unset($this->array[$offset]);
	}

	public function offsetGet($offset) {
		return $this->array[$offset];
	}
}

class DataFromPath {
	public $path_levels;

	public function __construct() {
		$uri = explode('?', $_SERVER['REQUEST_URI'], 2); // /maindir/app?a=123&b=23
		$path_absolute = $uri[0]; // /maindir/app
		$prefix_length = strlen(PATH_PREFIX); // /maindir
		$path_relative = substr($path_absolute, $prefix_length); // /app

		$path_levels = explode("/", $path_relative); // ['', app]
		array_shift($path_levels); // the first item is always empty

		$this->path_levels = $path_levels;
	}

	public function __get($key) {
		// /app/a/b/c/d  /key/value/  e/f/g
        if ($key == 'path_levels')
            return $this->path_levels;

		$value = "";
		$found_key = False;
		foreach ($this->path_levels as $level) {
			if ($found_key) { // occurs on the iteration after key find
				$value = $level;
				break;
			}

			if ($level === $key) {
				$found_key = True;
			}
		}

		$value = urldecode($value);

		return $value;
	}
}

class DataHandler {
	public $get, $post, $session;
	public function __construct() {
		$this->get = new DataFromArray($_GET);
		$this->post = new DataFromArray($_POST);
		$this->session = new DataFromArray($_SESSION);
		$this->path = new DataFromPath($_SESSION);
		$json = json_decode(file_get_contents('php://input'), true);
		$this->json = new DataFromArray($json);
	}
}
?>