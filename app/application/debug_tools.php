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
?>