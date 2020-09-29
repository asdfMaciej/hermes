<?php
namespace Model;
class Album extends \DBModel {
	protected static $table_name = "albums";
	protected static $primary_key = "album_id";

	public $album_id;
	public $title;
}
?>