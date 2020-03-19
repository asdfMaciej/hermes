<?php
namespace Model;
class User extends \DBModel {
	protected static $table_name = "users";
	protected static $primary_key = "id";

	public $id;
	public $login;
	public $password;
	public $name;
	public $register_date;

	public function register($db) {
		$this->password = password_hash($this->password, PASSWORD_DEFAULT);
		return self::save($db);
	}

	public static function login($db, $login, $password) {
		$user = static::getSingleItem($db, ["login" => $login]);

		if (!$user->id)
			return false;

		if (password_verify($password, $user->password))
			return $user;
		else
			return false;
	}
}
?>