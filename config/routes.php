<?php

use App\Controller\Admin\Auth;
use App\Controller\Index;
use App\Middleware\AlreadyLogin;
use App\Middleware\NeedLogin;
use Slim\App;
use App\Controller\Admin\Index as AdminIndex;

return function (App $app) {
    $container = $app->getContainer();


    $app->get('/admin/login', Auth::class . ':login')->setName('adminLogin')->add(new AlreadyLogin($container));
    $app->post('/admin/login', Auth::class . ':loginAction')->setName('adminLoginAction');

    $app->group('', function (App $app) {
        $app->get('/admin', AdminIndex::class . ':index')->setName('admin');
        $app->get('/admin/index', AdminIndex::class . ':index')->setName('adminIndex');
        $app->get('/admin/logout', Auth::class . ':logout')->setName('adminLogout');
    })->add(new NeedLogin($container));

    $app->get('/[{name}]', Index::class . ':index')->setName('fromIndex');



};
