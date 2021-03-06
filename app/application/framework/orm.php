<?php
class Model implements ArrayAccess {
	protected static $aliases = [];
	protected $computed = [];

	public function __construct() {}

	public function &__get($key) {
		if (array_key_exists($key, static::$aliases)) {
			$key = static::$aliases[$key];
		}

		if (array_key_exists($key, $this->computed)) {
			$value = $this->computed[$key]();
			return $value;
		}

		$value = &$this->{$key} ?? null;
		return $value;
	}

	public function __set($key, $value) {
		if (array_key_exists($key, static::$aliases)) {
			$this->{static::$aliases[$key]} = $value;
			return;
		}
		$this->{$key} = $value;
		return;
	}

	/*
	 * ArrayAccess implementation (offset methods): 
	 * Enables accessing the model as an Array: 
	*/
	public function offsetSet($offset, $value) {
		$this->{$offset} = $value;
	}

	public function offsetExists($offset) {
		return !is_null($this->{$offset}); 
	}

	public function offsetUnset($offset) {
		$this->{$offset} = null;
	}

	public function offsetGet($offset) {
		return $this->{$offset};
	}

	public function init() {} // virtual

	public static function getFields($model=null) {
		if (is_null($model)) {
			$model = get_called_class();
		}

		$class = new ReflectionClass($model);
		$public_fields = $class->getProperties(ReflectionProperty::IS_PUBLIC);

		$props = [];
		foreach ($public_fields as $property) {
			$property_name = $property->getName();
			$props[] = $property_name;
		}

		return $props; 
	}

	public function getValue($key) {
		return $this->{$key};
	}

	public function setValue($key, $value) {
		$this->{$key} = $value;
	}
}

class SessionModel extends Model {
	private $class_name;
	private $session_group = "SessionModels";

	public function __construct() {
		parent::__construct();

		$this->class_name = static::class;
		$this->initSession();

		$this->init();
	}

	public function __set($key, $value) {
		if (array_key_exists($key, static::$aliases)) {
			$key = static::$aliases[$key];
		}
		$this->setSessionField($key, $value);
	}

	public function &__get($key) {
		if (array_key_exists($key, static::$aliases)) {
			$key = static::$aliases[$key];
		}

		if (array_key_exists($key, $this->computed)) {
			$value = $this->computed[$key]();
			return $value;
		}
		return $this->getSessionField($key);
	}

	protected function initSession() {
		if (!isset($_SESSION[$this->session_group])) {
			$_SESSION[$this->session_group] = [];
		}

		if (!isset($_SESSION[$this->session_group][$this->class_name])) {
			$_SESSION[$this->session_group][$this->class_name] = $this->fields;
		}
	}

	protected function resetSession() {
		$_SESSION[$this->session_group][$this->class_name] = $this->fields;
	}

	protected function &getSessionField($field) {
		return $_SESSION[$this->session_group][$this->class_name][$field];
	}

	protected function setSessionField($field, $value) {
		$_SESSION[$this->session_group][$this->class_name][$field] = $value;
	}
}

class QueryBuilder {
	private $fields = "";
	private $table = "";
	private $where_alias = null;
	private $sql = null;
	private $where = "";
	private $order = "";
	private $group = "";
	private $limit = "";
	private $parameters = [];
	private $joins = [];

	private $statement;
	private $debug = false;

	public function __construct() {}
	public function select($fields) {
		if (!is_array($fields)) {
			$this->fields = $fields;
			return $this;
		}

		$this->fields = [];

		foreach ($fields as $key => $value) {
			// if key is numeric, then we just assume it's list of Alias.columns
			if (is_numeric($key)) {
				$this->fields[] = $value;
				continue;
			}

			// otherwise, we assume the key is a model name
			$table = $key::getClassName();
			
			// the columns dont need to have aliases
			if (is_array($value)) {
				foreach ($value as $column) {
					$this->fields[] = $table.".".$column;
				}
				continue;
			}

			// catchall: model name with a single field
			$this->fields[] = $table.".".$value;
		}

		// join all fields 
		$this->fields = implode(", ", $this->fields);
		return $this;
	}

	public function from($model) {
		$model_table = $model::getTableName();
		$alias = $model::getClassName();
		// FROM query
		$this->table = "`$model_table` AS $alias";

		// we need to store the alias for joining tables
		$this->where_alias = $alias;
		return $this;
	}

	public function where($condition) {
		if ($condition == "") {
			$this->where = "";
		} else {
			$this->where = "WHERE " . $condition;
		}
		return $this;
	}

	public function sql($sql=null) {
		$this->sql = $sql;
		return $this;
	}

	public function orderBy($column, $direction="asc") {
		$directions = ["asc", "desc", "ascending", "descending", ""];
		$valid = in_array(strtolower($direction), $directions);
		if (!$valid) {
			throw new Exception("Invalid direction - $direction");
		}

		$this->order = "";
		if ($column) {
			$this->order = "ORDER BY $column $direction";
		}
		return $this;
	}

	public function groupBy($condition) {
		if ($condition) {
			$this->group = "GROUP BY ".$condition;
		} else {
			$this->group = "";
		}
		return $this;
	}

	public function limit($n) {
		if ($n) {
			$this->limit = "LIMIT $n";
		} else {
			$this->limit = "";
		}
		return $this;
	}

	public function setParameters($params) {
		$this->parameters = $params;
		return $this;
	}

	public function addParameters($params) {
		foreach ($params as $param => $value) {
			$this->parameters[$param] = $value;
		}
		return $this;
	}

	public function setParameter($param, $value) {
		$this->parameters[$param] = $value;
		return $this;
	}

	public function leftJoin($model, $on, $model_b=null) {
		return $this->join("LEFT", $model, $on, $model_b);
	}
	public function rightJoin($model, $on, $model_b=null) {
		return $this->join("RIGHT", $model, $on, $model_b);
	}
	public function innerJoin($model, $on, $model_b=null) {
		return $this->join("INNER", $model, $on, $model_b);
	}

	public function execute($database) {
		$this->statement = $database->prepare($this->getQuery());
		$this->statement->execute($this->parameters);
		return $this;
	}

	public function getAll() {
		return $this->statement->fetchAll(PDO::FETCH_ASSOC);
	}

	public function getRow() {
		return $this->statement->fetch(PDO::FETCH_ASSOC);
	}

	public function debug() {
		$this->debug = true;
		return $this;
	}

	public function getQuery() {
		if ($this->sql)
			$q = $this->sql; 
		else
			$q = $this->buildQuery();
		
		if ($this->debug and DEBUG)
			echo "<div><h2>Query:</h2><pre>".$q."</pre></div>";

		return $q;
	}

	protected function buildQuery() {
		$q = "SELECT $this->fields\n";
		$q .= "FROM $this->table\n";

		$joins = implode("\n", $this->joins);
		$q .= "$joins";
		
		if ($this->where)
			$q .= "\n$this->where";
		
		if ($this->group)
			$q .= "\n$this->group";

		if ($this->order)
			$q .= "\n$this->order";

		if ($this->limit)
			$q .= "\n$this->limit";
		
		return $q;
	}

	private function join($method, $model, $on, $model_b=null) {
		// get class name for the specified model 
		$alias = $model::getClassName();

		// If model B isn't specified, use one from the where query
		if (is_null($model_b))
			$alias_b = $this->where_alias;
		else
			$alias_b = $model_b::getClassName();
		
		// use the same key for both tables
		$on_query = $alias_b.".".$on." = ".$alias.".".$on;

		$table_name = $model::getTableName();
		$query = "$method JOIN $table_name AS $alias\nON $on_query";
		
		// in case of multiple joins we use an array
		$this->joins[] = $query;
		return $this;
	}

}

class DBModel extends Model {
	/*
		Parts of code are based on: 
			https://catchmetech.com/en/post/94/how-to-create-an-orm-framework-in-pure-php
	*/
	protected static $table_name;
	protected static $foreign_fields = [];
	protected static $primary_key;
	protected static $aliases = [];

	public function __construct() {}

	public function init() {} // virtual

	public function save($database) {
		$key_value_pairs = [];
		$parameters = [];
		$primary = $this->getPrimaryKey();

		$props = static::getFields();
		foreach ($props as $property_name) {
			if ($property_name == $primary) {
				continue;
			}

			$value = $this->getValue($property_name);
			if (is_null($value)) {
				continue;
			}

			$param_name = ":$property_name";

			$key_value_pairs[] = "`$property_name` = $param_name";
			$parameters[$param_name] = $value;
		}

		$set_clause = implode(', ', $key_value_pairs);
		
		$query = '';
		$table_name = $this->getTableName();

		$primary_id = $this->getValue($primary);
		if ($primary_id > 0) {
			$query = "UPDATE `$table_name` SET $set_clause WHERE $primary = :$primary";
			$parameters[":$primary"] = $primary_id;
		} else {
			$query = "INSERT INTO `$table_name` SET $set_clause";
		}

		$statement = $database->prepare($query);
		$success = $statement->execute($parameters);

		if ($primary_id <= 0) {
			$this->setValue($primary, $database->lastInsertId());
		}

		if (!$success and DEBUG) {
			echo "<div><b>ORM Debug query error</b><br><pre>";
			print_r($statement->errorInfo());
			echo "</pre><br><pre>";
			echo $query;
			echo "</pre><br><pre>";
			print_r($parameters);
			echo "</pre></div>";
		}
		
		return $success;
	}

	public static function select($fields) {
		$query_builder = new QueryBuilder();
		return $query_builder->select($fields);
	}

	public static function sql($sql) {
		$query_builder = new QueryBuilder();
		return $query_builder->sql($sql);
	}

	public static function fromArray($array, $prefix="") {
		$model = get_called_class(); // it will be inherited
		$class = new ReflectionClass($model); // hence we need the child class

		$new_class = $class->newInstance();
		$public_fields = $class->getProperties(ReflectionProperty::IS_PUBLIC);

		$table = static::$table_name;
		$alias_fields = [];

		foreach ($public_fields as $property) {
			$property_name = $property->getName();
			$array_key = $prefix . $property_name;
			$property_exists = isset($array[$array_key]);

			if ($property_exists) {
				$value = $array[$array_key];
				$property->setValue($new_class, $value);
			}
		}

		$new_class->init();

		return $new_class;
	}

	private static function whereQuery($table_alias, $match) {
		$aliases = static::getAliases();
		$params = [];
		$where_q = [];
		foreach ($match as $field => $value) {
			$param = ":$field";
			$params[$param] = $value;

			if (array_key_exists($field, $aliases)) {
				$field = $aliases[$field];
			}			
			$where_q[] = "$table_alias.$field = $param";
		}
		$query = implode(" AND ", $where_q);
		return ["query" => $query, "parameters" => $params];
	}

	private static function getItemsQuery($match) {
		$table_alias = static::getClassName();

		$q = static::whereQuery($table_alias, $match);
		$where_query = $q["query"];
		$where_params = $q["parameters"];

		$row = static::select("$table_alias.*")
					->from(static::class)
					->where($where_query)
					->setParameters($where_params);
		return $row;
	}

	public static function delete($db, $condition=[]) {
		if (!$condition)
			throw new \Exception("No condition set for delete!");

		$table = static::$table_name;
		$where = static::whereQuery($table, $condition);
		$query = $where['query'];
		static::sql("DELETE FROM $table WHERE $query")
					->setParameters($where['parameters'])
					->execute($db);
		return true;
	}

	public static function getSingleItem($db, $match=[]) {
		$row = static::getItemsQuery($match)
					->execute($db)
					->getRow();
		return static::fromArray($row);
	}

	public static function getItems($db, $match=[]) {
		$rows = static::getItemsQuery($match)
					->execute($db)
					->getAll();

		return $rows;
	}

	public static function getTableName() {
		return static::$table_name;
	}

	public static function getClassName() {
		return basename(str_replace('\\', '/', static::class)); // removes namespace
	}

	public static function getPrimaryKey() {
		return static::$primary_key;
	}

	public static function getForeignFields() {
		return static::$foreign_fields;
	}

	public static function getAliases() {
		return static::$aliases;
	}
}
?>