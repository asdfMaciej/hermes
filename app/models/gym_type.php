<?php
namespace Model;
class GymType extends \DBModel {
	protected static $table_name = "gym_types";
	protected static $primary_key = "type_id";

	public $type_id;
	public $gym_type;
}
?>