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

	public function get() {
        $id = $this->data->path->profiles;
        if (!$id)
            return;
        $path = $this->data->path->path_levels;
        if ($path[1] != 'profiles') {
            throw new \Exception('Path has changed!');
        }
        $action = $path[3] ?? ''; // following / followers
        if ($action == 'following') {
            return $this->generateAndSet(User::getFollowedProfiles($this->database, $id, $this->account->user_id), 200);
        } elseif ($action == 'followers') {
            return $this->generateAndSet(User::getProfileFollowers($this->database, $id, $this->account->user_id), 200);
        }
        return $this->generateAndSet(User::getProfileById($this->database, $id, $this->account->user_id), 200);
    }
}

new Page();
?>