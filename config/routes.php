<?php

use App\Controller\Admin\Auth;
use App\Controller\Index;
use App\Middleware\AlreadyLogin;
use Slim\App;
use App\Controller\Admin\Index as AdminIndex;

return function (App $app) {
    $container = $app->getContainer();

    $app->get('/admin', AdminIndex::class . ':index')->setName('admin');
    $app->get('/admin/index', AdminIndex::class . ':index')->setName('adminIndex');
    $app->get('/admin/login', Auth::class . ':login')->setName('adminLogin')->add(new AlreadyLogin($container));

    $app->get('/[{name}]', Index::class . ':index')->setName('fromIndex');


    $app->get('/admin/logout', Auth::class . ':logout')->setName('adminLogout');
    $app->post('/admin/login', Auth::class . ':loginAction')->setName('adminLoginAction');
};
