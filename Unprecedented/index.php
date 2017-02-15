<?php

require('app/Autoloader.php');

$loader = new App\Autoloader();
$loader->addPlugins();
$loader->addProviders();

$offset_path = '/Projects/phpAlgorithms/Unprecedented';

$uri = urldecode(
    substr(parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH), strlen($offset_path))
);

echo $uri  . '<br>';

$app = App\App::make();

//$class = new App\StaticFactory();

//$auth = new Clake\Test();

//$module = new Clake\Core\module();