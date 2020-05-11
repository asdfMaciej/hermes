<?php
include_once __DIR__ . "/env/config.php";
include_once ROOT_PATH . "/application/app.php";

$router = new Router();
$router->page404 = "404.php";

$router->route('', 'index.php');
$router->route('login\/?', 'login.php');
$router->route('register\/?', 'register.php');
$router->route('workout\/add\/?', 'workouts/add.php');
$router->route('workout\/\d+\/?', 'workouts/view.php');
$router->route('gym\/\d+\/?', 'gyms/view.php');
$router->route('profile\/\d+\/?', 'profiles/view.php');
$router->route('profile\/\d+\/follow\/?', 'profiles/follow.php');
$router->route('profile\/\d+\/unfollow\/?', 'profiles/unfollow.php');
$router->route('upload\/avatar\/?', 'upload/avatar.php');

$router->route('api\/login\/?', 'api/login.php');
$router->route('api\/workouts\/?', 'api/workouts.php');
$router->route('api\/exercise_types\/?', 'api/exercise_types.php');
$router->route('api\/gyms\/?', 'api/gyms.php');


$router->execute();
?>