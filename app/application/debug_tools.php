<?php
if (!DEBUG)
	die();

if (defined('IMPORT_MODELS') && IMPORT_MODELS)
	foreach (glob(ROOT_PATH . "/application/models/*.php") as $filename)
		include_once $filename;

if (defined('AS_RAW_TEXT') && AS_RAW_TEXT)
	header("Content-Type: text/plain");

/* https://stackoverflow.com/questions/7153000/get-class-name-from-file */
function getClassFromFile($filename) {
	$fp = fopen($filename, 'r');
	if (!$fp)
		return False;

	$class = $buffer = '';
	$i = 0;
	while (!$class) {
		if (feof($fp))
			return null;

		$buffer .= fread($fp, 512);
		if (preg_match('/class\s+(\w+)(.*)?\{/', $buffer, $matches)) {
			return $matches[1];
		}
	}
}

/* https://gist.github.com/naholyr/1885879 */
function getNamespaceFromFile($filename) {
	$fp = fopen($filename, 'r');
	if (!$fp)
		return False;

	$src = fread($fp, filesize($filename));
	if (preg_match('#^namespace\s+(.+?);#sm', $src, $m)) {
		return $m[1];
	}
	return null;
}

function getTableColumns($database, $table) {
	$statement = $database->prepare("
		SELECT * from information_schema.columns
		WHERE TABLE_SCHEMA = :schema
		AND TABLE_NAME = :table 
	");
	$statement->execute([":schema" => DB_DATABASE, ":table" => $table]);
	if (!$statement)
		return False;

	return $statement->fetchAll(PDO::FETCH_ASSOC);
}

function out($txt="") {
	echo $txt . "\n";
}

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

?>