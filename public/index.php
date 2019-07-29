<?php

use App\Utils\Val;

if (PHP_SAPI == 'cli-server') {
    // To help the built-in PHP dev server, check if the request was actually for
    // something which should probably be served as a static file
    $url = parse_url($_SERVER['REQUEST_URI']);
    $file = __DIR__ . $url['path'];
    if (is_file($file)) {
        return false;
    }
}

define('RUN_SCRIPT_DIR', __DIR__);
define('APP_DIR', __DIR__ . '/..');

require __DIR__ . '/../vendor/autoload.php';

header('Cache-Control: no cache');

// 设置 session_name
session_name('CSESSID');

session_start();

// load env
$dotenv = \Dotenv\Dotenv::create(__DIR__ . '/../');
$dotenv->load();

// Instantiate the app
$settings = require __DIR__ . '/../config/settings.php';

ini_set('date.timezone', $settings['tz']);

$app = new \Slim\App($settings);

// Set up dependencies
$dependencies = require __DIR__ . '/../config/dependencies.php';
$dependencies($app);

// Register middleware
$middleware = require __DIR__ . '/../config/middleware.php';
$middleware($app);

// Register routes
$routes = require __DIR__ . '/../config/routes.php';
$routes($app);

Val::getInstance()['container'] = $app->getContainer();
// Run app
$app->run();
