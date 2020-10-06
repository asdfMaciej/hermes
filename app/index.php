<?php
include_once __DIR__ . "/application/config.php";
include_once ROOT_PATH . "/application/app.php";

$router = new Router();
$router->page404 = "404.php";

// accounts
$router->route('', 'index.php');

// profile
$router->route('profile\/\d+\/?', 'profiles/view.php');
$router->route('upload\/avatar\/?', 'upload/avatar.php');
$router->route('settings\/?', 'profiles/settings.php');
$router->route('search\/?', 'profiles/search.php');

// workouts
$router->route('workout\/add\/?', 'workouts/add.php');
$router->route('workout\/\d+\/edit\/?', 'workouts/add.php');
$router->route('workout\/\d+\/?', 'workouts/view.php');
$router->route('workout\/\d+\/react\/?', 'workouts/react.php');
$router->route('workout\/\d+\/unreact\/?', 'workouts/unreact.php');
// gym
$router->route('gym\/\d+\/?', 'gyms/view.php');

// exercises
$router->route('exercise\/\d+\/?', 'exercise/view.php');

$router->route('routines\/?', 'routines/view.php');


// api
$router->route('api\/login\/?', 'api/login.php');
$router->route('api\/workouts\/?', 'api/workouts.php');
$router->route('api\/workouts\/\d+\/?', 'api/workouts.php');

$router->route('api\/newsfeed\/?', 'api/newsfeed.php');
$router->route('api\/routines\/?', 'api/routines.php');
$router->route('api\/routines\/\d+\/?', 'api/routines.php');

$router->route('api\/exercise_categories\/?', 'api/exercise_categories.php');
$router->route('api\/exercise_types\/?', 'api/exercise_types.php');
$router->route('api\/exercises\/past\/?', 'api/exercises_past.php');

$router->route('api\/gyms\/?', 'api/gyms.php');
$router->route('api\/gyms\/\d+\/newsfeed\/?', 'api/newsfeed.php');

$router->route('api\/reactions\/?', 'api/reactions.php');

$router->route('api\/profiles\/\d+\/?', 'api/profiles.php');
$router->route('api\/profiles\/?', 'api/profiles.php');
$router->route('api\/profiles\/\d+\/following\/?', 'api/profiles.php');
$router->route('api\/profiles\/\d+\/followers\/?', 'api/profiles.php');
$router->route('api\/profiles\/\d+\/newsfeed\/?', 'api/newsfeed.php');
$router->execute();
?>