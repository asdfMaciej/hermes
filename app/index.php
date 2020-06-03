<?php
include_once __DIR__ . "/env/config.php";
include_once ROOT_PATH . "/application/app.php";

$router = new Router();
$router->page404 = "404.php";

// accounts
$router->route('', 'index.php');

// profile
$router->route('profile\/\d+\/?', 'profiles/view.php');
$router->route('profile\/\d+\/follow\/?', 'profiles/follow.php');
$router->route('profile\/\d+\/unfollow\/?', 'profiles/unfollow.php');
$router->route('upload\/avatar\/?', 'upload/avatar.php');

// workouts
$router->route('workout\/add\/?', 'workouts/add.php');
$router->route('workout\/\d+\/?', 'workouts/view.php');

// gym
$router->route('gym\/\d+\/?', 'gyms/view.php');

// exercises
$router->route('exercises\/?', 'exercises/view_all.php');

// api
$router->route('api\/login\/?', 'api/login.php');
$router->route('api\/workouts\/?', 'api/workouts.php');
$router->route('api\/exercise_categories\/?', 'api/exercise_categories.php');
$router->route('api\/gyms\/?', 'api/gyms.php');


$router->execute();
?>