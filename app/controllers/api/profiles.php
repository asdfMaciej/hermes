<?php
namespace Web\Pages;
use \Model\User;

class Page extends \APIBuilder {
	public function post() {
		$user_id = $this->data->json["user_id"] ?? 0;
		$action = $this->data->json["action"] ?? "";

		if (!$user_id || !($action == "follow" || $action == "unfollow")) {
			return $this->generateAndSet([], 400);
		}
		
		try {
			if ($action == "follow") {
				User::follow($this->database, $this->account->user_id, $user_id);
			} elseif ($action == "unfollow") {
				User::unfollow($this->database, $this->account->user_id, $user_id);
			}
		} catch (\Exception $e) {
			return $this->generateAndSet([], 400);
		}
		
		return $this->generateAndSet([], 200);
	}
}

new Page();
?>