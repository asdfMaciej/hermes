<?php
namespace Web\Pages;
use \Model\Workout;
use \Model\Exercise;
use mysql_xdevapi\Exception;

class Page extends \APIBuilder {
	public function get() {
        $params = [
            'before_id' => $this->data->get->before
        ];

        if ($profile_id = $this->data->path->profiles) {
            $workouts = Workout::getNewsfeedForUser($this->database, $profile_id, $this->account->user_id, $params);
        } elseif ($gym_id = $this->data->path->gyms) {
            $workouts = Workout::getNewsfeedForGym($this->database, $gym_id, $this->account->user_id, $params);
        } else {
            $workouts = Workout::getNewsfeedList($this->database, $this->account->user_id, $params);
        }
        
        
        return $this->generateAndSet(["workouts" => $workouts], 200);       
    }
}

new Page();