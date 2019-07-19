<?php

use App\Twig\Extension\Breadcrumb;
use App\Twig\Extension\DefaultExtra;
use App\Twig\Extension\Old;
use App\Twig\Extension\Paginate;
use Illuminate\Database\Capsule\Manager;
use Monolog\Handler\StreamHandler;
use Monolog\Processor\UidProcessor;
use Slim\App;
use Slim\Flash\Messages;
use Slim\Http\Environment;
use Slim\Views\Twig;
use Slim\Views\TwigExtension;

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
        $uri = \Slim\Http\Uri::createFromEnvironment(new Environment($_SERVER));
        $view->addExtension(new TwigExtension($router, $uri));
        // 注入自己的扩展
        $view->addExtension(new Paginate());
        $view->addExtension(new Old());
        $view->addExtension(new DefaultExtra());
        $view->addExtension(new Breadcrumb());

        return $view;
    };

    // monolog
    $container['logger'] = function ($c) {
        $settings = $c->get('settings')['logger'];
        $logger = new \Monolog\Logger($settings['name']);
        $logger->pushProcessor(new UidProcessor());
        $logger->pushHandler(new StreamHandler($settings['path'], $settings['level']));
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

    // Register provider
    $container['flash'] = function () {
        return new Messages();
    };
};
