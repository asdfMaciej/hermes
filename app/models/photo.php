<?php
namespace Model;
class Photo extends \DBModel {
	protected static $table_name = "photos";
	protected static $primary_key = "photo_id";

	public $photo_id;
	public $album_id;
	public $path;
	public $date;
	public $width;
	public $height;

	public static function getForAlbumId($database, $album_id) {
		return static::getItems($database, ["album_id" => $album_id]);
	}

	public static function getForId($database, $photo_id) {
		return static::getSingleItem($database, ["photo_id" => $photo_id]);
	}
}
?>