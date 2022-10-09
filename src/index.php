<?php
require './vendor/autoload.php';

$app = new \Slim\App;
$container = $app->getContainer();

/**
 * Container Definitions
 */

$container['db'] = function($c) {
	$database = $user = $password = "sakila";
	$host = "mysql";

	$db = new PDO("mysql:host={$host};dbname={$database};charset=utf8", $user, $password);
	$db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
	return $db;
};

$container['movieData'] = function($c) {
	return new \DataLayer\MovieData($c['db']);
};

/**
 * Routes
 */
;
$app->get('/movies/list', \Controllers\MovieController::class . ':searchByCriteria');
$app->get('/movie/{id}', \Controllers\MovieController::class . ':getMovie');
$app->post('/movie/add', \Controllers\MovieController::class . ':addMovie');

$app->run();

