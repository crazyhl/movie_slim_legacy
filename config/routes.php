<?php

use App\Controller\Admin\Auth;
use App\Controller\Index;
use Slim\App;

return function (App $app) {
    $container = $app->getContainer();

    $app->get('/[{name}]', Index::class . ':index');

    $app->get('/admin/login', Auth::class. ':login');
    $app->post('/admin/login', Auth::class. ':loginAction');
};
