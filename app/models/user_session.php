<?php
namespace Model;
class UserSession extends \SessionModel {
	protected $fields = [
		"user_id" => 0,
		"login" => "",
		"password" => "",
		"name" => "",
		"register_date" => "",
		"avatar" => "",
		"first_name" => ""
	];

	public function isLoggedIn() {
		return $this->user_id != 0 && $this->login;
	}

	public function loginUser($user_model) {
		foreach ($this->fields as $field_name => $val) {
			$this->{$field_name} = $user_model->{$field_name};
		}

		$this->first_name = explode(" ", $this->name)[0];
	}

	public function logout() {
		$this->resetSession();
	}
}
?>