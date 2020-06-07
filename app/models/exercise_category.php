<?php
namespace Model;
class ExerciseCategory extends \DBModel {
	protected static $table_name = "exercise_categories";
	protected static $primary_key = "category_id";

	public $category_id;
	public $parent_id;
	public $name;

	protected static function buildTree($categories, $exercises) {
		$children = [];
		$category_exercises = [];

		foreach ($exercises as $exercise) {
			$category_id = $exercise['category_id'];
			if (!isset($category_exercises[$category_id])) {
				$category_exercises[$category_id] = [];
			}

			$category_exercises[$category_id][] = $exercise;
		}

		foreach($categories as &$item) {
			$item['exercises'] = [];
			if (array_key_exists($item['category_id'], $category_exercises)) {
				$item['exercises'] = $category_exercises[$item['category_id']];
			}

			$children[$item['parent_id']][] = &$item;
		}

		unset($item);

		foreach($categories as &$item) {
			if (isset($children[$item['category_id']])) {
				$item['categories'] = $children[$item['category_id']];
			} else {
				$item['categories'] = [];
			}
		}

		return $children[0];
	}

	public static function getTree($database) {
		$categories = static::select([
			"category_id", "coalesce(parent_id, 0) as parent_id", "name"
		])
		->from(static::class)
		->execute($database)
		->getAll();

		$exercises = ExerciseType::getItems($database);
		return static::buildTree($categories, $exercises);
	}
}
?>