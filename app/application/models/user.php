<?php
namespace Model;
class User extends \DBModel {
	protected static $table_name = "users";
	protected static $primary_key = "user_id";

	public $user_id;
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

		if (!$user->user_id)
			return false;

		if (password_verify($password, $user->password))
			return $user;
		else
			return false;
	}

	public static function getProfileById($database, $id) {
		$row = static::select("u.user_id, u.login, u.name, u.register_date, u.avatar")
				->from(static::class, "u")
				->where("u.user_id = :id")
				->setParameter(":id", $id)
				->execute($database)
				->getRow();

		return $row;
	}
}
?>