<?php
/*
 * File used to quickly make internal changes. 
 * Creating respective templates, controllers and routing
 * 	is an unnecessary waste of time for those tasks.
 * Debug only.
*/

const IMPORT_MODELS = False;
const AS_RAW_TEXT = True;

include_once __DIR__ . "/env/config.php";
include_once ROOT_PATH . "/framework/include.php";
include_once ROOT_PATH . "/application/debug_tools.php";

class DBModelTest {
	protected $namespace = "Model";

	public function run($path) {
		$classname = getClassFromFile($path);
		$namespace = getNamespaceFromFile($path);

		if (is_null($classname))
			return [False, 'No class declaration in the model file!'];
		
		if (is_null($namespace))
			return [False, 'Namespace unspecified in the model file!'];

		if (!$classname || !$namespace)
			return [False, 'Unable to open the model file!'];
		
		if ($namespace !== $this->namespace)
			return [False, "Namespace must be '".$this->namespace."'!"];

		if (!include($path))
			return [False, 'Error during including the model file!'];

		$class = $namespace . "\\" . $classname;
		$model = new $class();

		if (!is_subclass_of($model, "\\DBModel"))
			if (is_subclass_of($model, "\\Model"))
				return [True, "$classname isn't a database model - omitting"];
			else
				return [False, "$classname isn't a \Model descendant!"];
		
		$table = $model::getTableName();
		$primary_key = $model::getPrimaryKey();

		if (!$table)
			return [False, "$classname has unspecified table name!"];

		if (!$primary_key)
			return [False, "$classname has unspecified primary key!"];


		$db_class = new DBClass();
		$db = $db_class->getConnection();
		if (!$db)
			return [False, "Failure to establish database connection!"];

		$columns = $model::getFields();
		$db_columns = getTableColumns($db, $table);
		if (!$db_columns)
			return [False, "$classname - retrieving columns for table $table failed"];

		$fully_correct = True;
		$message = "";
		foreach ($db_columns as $column) {
			$column_name = $column["COLUMN_NAME"];
			$column_key = $column["COLUMN_KEY"];
			if (in_array($column_name, $columns)) {
				$columns = array_diff($columns, [$column_name]); // removes the column
				if ($column_key == "PRI" && $column_name != $primary_key) {
					$fully_correct = False;
					$message .= "\n[!] $classname - $column_name is set as the primary key in the database, but not in the model!";
				} elseif ($column_key != "PRI" && $column_name == $primary_key) {
					$fully_correct = False;
					$message .= "\n[!] $classname - $column_name is set as the primary key in the model, but isn't one in the database!";
				}
			} else {
				$fully_correct = False;
				$message .= "\n[!] $classname - $column_name not found in table $table!";
			}
		}

		if ($fully_correct)
			$message = "OK. Every DB field found in model.";

		foreach ($columns as $column_name) {
			$fully_correct = False;
			$message .= "\n[*] $classname - extra public property found: \$$column_name";
		}

		$message = trim($message);
		return [$fully_correct, $message];
	}
}

$test = new DBModelTest();
foreach (glob(ROOT_PATH . "/application/models/*.php") as $model_path) {
	out("Checking: $model_path");
	$result = $test->run($model_path);
	out($result[1]);
	out();
}

?>