<?php

use App\Controller\Admin\Auth;
use App\Controller\Index;
use App\Middleware\AlreadyLogin;
use Slim\App;
use App\Controller\Admin\Index as AdminIndex;

return function (App $app) {
    $container = $app->getContainer();

    $app->get('/admin', AdminIndex::class . ':index');
    $app->get('/admin/index', AdminIndex::class . ':index');

    $app->get('/[{name}]', Index::class . ':index');


    $app->get('/admin/login', Auth::class . ':login')->add(new AlreadyLogin($container));
    $app->post('/admin/login', Auth::class . ':loginAction');


};
