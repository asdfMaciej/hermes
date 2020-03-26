<?php
namespace Model;
class Gym extends \DBModel {
	protected static $table_name = "gyms";
	protected static $primary_key = "gym_id";

	public $gym_id;
	public $album_id;
	public $name;
	public $lat;
	public $long;
}
?>