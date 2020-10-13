<?php
namespace Web\Pages;
use \Model\Workout;
use \Model\Exercise;
use \Model\Routine;
use \Model\Album;
use \Model\Photo;
use \Model\RoutineExerciseType;

include_once ROOT_PATH . "/application/lib/bulletproof/bulletproof.php";
include_once ROOT_PATH . "/application/lib/bulletproof/utils/func.image-resize.php";
include_once ROOT_PATH . "/application/lib/bulletproof/utils/func.image-crop.php";

class Page extends \APIBuilder {
	protected function addRoutine() {
		$routine = $this->data->json["routine"];
		$exercises = $this->data->json["exercises"];
		$name = $routine["name"];
		if (!$name)
			throw new \Exception("Routine doesnt have a name!");

		$routine["user_id"] = $this->account->user_id;
		Routine::fromArray($routine)->save($this->database);

		$routine_id = $this->database->lastInsertId();

		// now, we need to count reps
		$exercise_types = [];

		/* the code below counts sets for each exercise
		it can occur that exercises are for ex. AABBAAA so it cant be 
		A->5, B->2, it has to be A->2, B->2, A->3 */
		foreach ($exercises as $exercise) {
			$type_id = $exercise['type_id'];
			if (count($exercise_types)) {
				$last = &$exercise_types[count($exercise_types)-1];
				if ($last['type_id'] == $type_id) {
					$last['sets'] += 1;
					continue;
				}
			}
			$exercise['sets'] = 1;
			$exercise['routine_id'] = $routine_id;
			$exercise_types[] = $exercise;
		}

		// add it to the db
		foreach ($exercise_types as $exercise_type) {
			RoutineExerciseType::fromArray($exercise_type)->save($this->database);
		}

		return $routine_id; // we're set
	}

	protected function addPhotos($album_id) {
		// adds photos passed by json as base64
		$images = $this->data->json['images'] ?? [];
		
		if (!$images) {
			return;
		}

		foreach ($images as $filename => $image) {
			$this->addPhoto($filename, $image, $album_id);
		}
		
	}

	protected function addPhoto($filename, $image, $album_id) {
		// create a temporary file and get its path
		$file = tmpfile();
		$path = stream_get_meta_data($file)['uri'];

		// split the (...)base64, prefix and extract mime type
		list($type, $data) = explode(';', $image);
		list(, $data) = explode(',', $data);
		list(, $type)= explode(':', $type);

		// decode the image from base64
		$data = base64_decode($data);

		// save the image into the tmp file and get its size
		file_put_contents($path, $data);
		$size = filesize($path);

		// todo: change name to according filename and store filename in db
		$file_array = [
			"name" => $filename,
			"type" => $type,
			"tmp_name" => $path,
			"error" => 0,
			"size" => $size
		];

		// create Bulletproof image - max limit 20 MB, jpeg/jpg/png, uploads to uploads/img
		// todo: change max image limits
		$image = new \Bulletproof\Image($file_array);
		$image->setSize(1, 1000 * 1000 * 20); // min / max mb

		$image->setMime(['jpeg', 'jpg', 'png']);
		$image->setLocation(ROOT_PATH . "/uploads/img");

		if ($success = $image->upload()) {} else {
			throw new \Exception($image->getError());
		}

		// convert the image to 600x600 jpg
	    $path_before = $image->getFullPath();
	    $path = $image->getLocation().'/'.$image->getName().'.jpg';
	    $width = $image->getWidth();
	    $height = $image->getHeight();

	    // todo: create 3 size versions
		\Bulletproof\Utils\resize(
            $path_before,
			"jpg",
			$width,
            $height,
            600, 600,
			true
		);

		// move the file to .jpg path and get its size 
		rename($path_before, $path);
        list($w, $h) = getimagesize($path);

        // save it to the database and quit
		return Photo::fromArray([
			"album_id" => $album_id,
			"filename" => $filename,
			"path" => "uploads\\img\\" . $image->getName() . ".jpg",
			"width" => $w,
			"height" => $h
		])->save($this->database);
	}

	public function post() {
		$workout = $this->data->json["workout"] ?? [];

		$editing = $workout["workout_id"] ?? null;
		// if editing a workout, we need to verify the user
		if ($editing) {
		    $existing_workout = Workout::getSingleItem($this->database, ["workout_id" => $workout["workout_id"]]);
		    if ($existing_workout['user_id'] !== $this->account->user_id) {
                return $this->generateAndSet(["error" => "You're not the user!"], 400);
            }
        }

        $workout["user_id"] = $this->account->user_id;
		$exercises = $this->data->json["exercises"] ?? [];

		// if something fails, we need to revert everything
		$this->database->beginTransaction();
		try {
			// create an album for each new workout
			if (!$editing) {
				Album::fromArray(["title" => "#W(" . $workout['user_id'] . ') ' .  $workout["title"]])->save($this->database);
				$album_id = $this->database->lastInsertId();
				$workout['album_id'] = $album_id;
			} else {
				$album_id = $workout['album_id'];
			}

			Workout::fromArray($workout)->save($this->database);
			$workout_id = $editing ? $editing : $this->database->lastInsertId();

			// we need to delete the past exercises if editing
			if ($editing) {
				Photo::delete($this->database, ["album_id" => $album_id]);
			    Exercise::delete($this->database, ["workout_id" => $workout_id]);
            }

            $this->addPhotos($album_id);
			foreach ($exercises as $exercise) {
			    $exercise["exercise_id"] = 0;
			    // comma separator doesn't work

                if ($exercise["weight"] ?? false) {
                    $exercise["weight"] = str_replace(",", ".", $exercise["weight"]);
                    $exercise["weight"] = trim($exercise["weight"]);
                }

			    if ($exercise["reps"] ?? false) {
                    $exercise["reps"] = trim($exercise["reps"]);
                }

                if ($exercise["duration"] ?? false) {
                    $exercise["duration"] = trim($exercise["duration"]);
                }

			    // for the moment, leave it as a client-side feature due to UI fail
			    $exercise["failure"] = 0;

			    if ($exercise["show_reps"] && intval($exercise["reps"]) <= 0)
			        throw new \Exception("Reps <= 0");

			    if ($exercise["show_duration"] && intval($exercise["duration"]) <= 0)
			        throw new \Exception("Duration <= 0");

			    if ($exercise["show_weight"] && floatval($exercise["weight"]) < 0)
			        throw new \Exception("Weight < 0");

			    // todo: first query the db for every exercise type
			    // and check it that way if we should store reps/weight/duration
			    if (!$exercise["show_weight"])
			    	unset($exercise["weight"]);

			    if (!$exercise["show_duration"])
			    	unset($exercise["duration"]);

			    if (!$exercise["show_reps"])
			    	unset($exercise["reps"]);

				$exercise["workout_id"] = $workout_id;
				Exercise::fromArray($exercise)->save($this->database);
			}

			// add a new routine if asked by user
			if ($this->data->json["routine"]["add"]) {
				$this->addRoutine();
			}

		} catch (\Exception $e) {
			error_log($e);
			$this->database->rollBack();
			return $this->generateAndSet([], 400);
		}

		$this->database->commit();
		return $this->generateAndSet(["workout_id" => $workout_id], 201);
	}

	public function delete() {
		$id = $this->data->path->workouts;
		$workout = Workout::getSingleItem($this->database, ["workout_id" => $id]);
		if (!$id)
			return $this->generateAndSet([], 400);

		if ($this->account->user_id !== $workout["user_id"])
			return $this->generateAndSet([], 401);

		Workout::delete($this->database, ["workout_id" => $id]);
		return $this->generateAndSet([], 200);
	}

	public function get() {
        $id = $this->data->path->workouts;
        $workout = Workout::getSingleItem($this->database, ["workout_id" => $id]);
        if (!$workout)
            return $this->generateAndSet([], 400);

        $exercises = Exercise::getWithTypes($this->database, $id);
        $photos = Photo::getForWorkout($this->database, $id);
        return $this->generateAndSet(compact("workout", "exercises", "photos"), 200);
    }
}

new Page();