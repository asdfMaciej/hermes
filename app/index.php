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
/*
$router->route('category(?:\/([^\/]+)\/?|\/?)', 'category.php');
$router->route('order(?:\/([^\/]*)\/?|\/?)', 'order.php');
$router->route('product\/([^\/]+)\/?', 'product.php');
$router->route('basket\/?', 'basket.php');
*/
$router->execute();
?>