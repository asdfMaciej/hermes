<?php
require_once ROOT_PATH . "/lib/OAuth2/Autoloader.php";
OAuth2\Autoloader::register();

class UsersStorage implements \OAuth2\Storage\UserCredentialsInterface {
    public function checkUserCredentials($username, $password) {
    	$db_c = new DBClass();
    	$db = $db_c->getConnection();

    	return \Model\User::login($db, $username, $password) != false;
    }

    public function getUserDetails($username) {
    	$db_c = new DBClass();
    	$db = $db_c->getConnection();

    	$user = \Model\User::getSingleItem($db, ["login" => $username]);
    	if (!$user->user_id)
    		return false;

    	return ["user_id" => $user->user_id];
    }
}

/* curl 127.0.0.1/app/test.php -d 'client_id=1&grant_type=password&username=u&password=p' */

$storage = new \OAuth2\Storage\Pdo([
	'dsn' => 'mysql:dbname='.DB_DATABASE.';host='.DB_HOST, 
	'username' => DB_USERNAME,
	'password' => DB_PASSWORD
]);

$oauth_server = new \OAuth2\Server($storage);
$users_storage = new UsersStorage();
$oauth_server->addGrantType(new \OAuth2\GrantType\UserCredentials($users_storage));
$oauth_server->handleTokenRequest(OAuth2\Request::createFromGlobals())->send();