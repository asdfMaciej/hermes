<?php
namespace Model;
class Photo extends \DBModel {
	protected static $table_name = "photos";
	protected static $primary_key = "photo_id";

	public $photo_id;
	public $album_id;
	public $path;
	public $filename;
	public $date;
	public $width;
	public $height;

	public static function getForAlbumId($database, $album_id) {
		return static::getItems($database, ["album_id" => $album_id]);
	}

	public static function getForId($database, $photo_id) {
		return static::getSingleItem($database, ["photo_id" => $photo_id]);
	}

	public static function getForWorkout($database, $workout_id) {
		$photos_query = "
SELECT w.workout_id, p.*
	FROM workouts AS w 
INNER JOIN albums AS a
	ON a.album_id = w.album_id
INNER JOIN photos AS p
	ON p.album_id = a.album_id
WHERE w.workout_id = :workout_id
		";

		$photos = static::sql($photos_query)
			->setParameter(":workout_id", $workout_id)
			->execute($database)
			->getAll();

		return $photos;
	}
}
?>