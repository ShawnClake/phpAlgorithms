<?php

require('app/Autoloader.php');

/*
 * Enable the Autoloader
 *
 */
$loader = new App\Autoloader();
$loader->addPlugins();
$loader->addProviders();

/*
 * Using a factory method, make an application
 *
 */
$app = App\App::make();

/*
 * After the application is made, preform a route.
 * TODO: After routing, the application needs to build a response
 * TODO: Finally after a response is built and sent, empty the socket queue.
 *
 */
$app->route();

/*$offset_path = '/Projects/phpAlgorithms/Unprecedented';

$uri = urldecode(
    substr(parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH), strlen($offset_path))
);

echo $uri  . '<br>';

echo path_root();*/

//$class = new App\StaticFactory();

//$auth = new Clake\Test();

//$module = new Clake\Core\module();