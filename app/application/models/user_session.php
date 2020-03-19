<?php
namespace Model;
class UserSession extends \SessionModel {
	protected $fields = [
		"id" => 0,
		"login" => "",
		"password" => "",
		"name" => "",
		"register_date" => ""
	];

	public function isLoggedIn() {
		return $this->id != 0 && $this->login;
	}

	public function loginUser($user_model) {
		foreach ($this->fields as $field_name => $val) {
			$this->{$field_name} = $user_model->{$field_name};
		}
	}

	public function logout() {
		$this->resetSession();
	}
}
?>