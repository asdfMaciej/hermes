<?php
namespace Web\Pages;
use \Model\Routine;
use \Model\Exercise;
use mysql_xdevapi\Exception;

class Page extends \APIBuilder {
	public function get() {
        $id = $this->data->path->routines;

        if ($id) { // get specified by id
        	$exercises = Routine::getExerciseTypes($this->database, $id);
        	$routine = Routine::getSingleItem($this->database, ["routine_id" => $id]);
			return $this->generateAndSet(["exercises" => $exercises, "routine" => $routine], 200);
        } else { // get all
        	return $this->generateAndSet(["routines" => Routine::getRoutines($this->database)], 200);
        }
        
    }
}

new Page();