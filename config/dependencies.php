<?php

use Illuminate\Database\Capsule\Manager;
use Slim\App;
use Slim\Views\Twig;

return function (App $app) {
    $container = $app->getContainer();

    // Register component on container
    $container['view'] = function ($c) {
        $settings = $c->get('settings')['view'];

        $view = new Twig($settings['template_path'], [
            'cache' => $settings['cache'],
        ]);

        // Instantiate and add Slim specific extension
        $router = $c->get('router');
        $uri = \Slim\Http\Uri::createFromEnvironment(new \Slim\Http\Environment($_SERVER));
        $view->addExtension(new \Slim\Views\TwigExtension($router, $uri));

        return $view;
    };

    // monolog
    $container['logger'] = function ($c) {
        $settings = $c->get('settings')['logger'];
        $logger = new \Monolog\Logger($settings['name']);
        $logger->pushProcessor(new \Monolog\Processor\UidProcessor());
        $logger->pushHandler(new \Monolog\Handler\StreamHandler($settings['path'], $settings['level']));
        return $logger;
    };

    // Service factory for the ORM
    $container['db'] = function ($container) {
        $capsule = new Manager;
        $capsule->addConnection($container['settings']['db']);

        $capsule->setAsGlobal();
        $capsule->bootEloquent();

        return $capsule;
    };
};
