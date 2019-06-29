<?php

use App\Controller\Admin\Auth;
use App\Controller\Index;
use Slim\App;
use App\Controller\Admin\Index as AdminIndex;

return function (App $app) {


    $app->get('/admin', AdminIndex::class . ':index');
    $app->get('/admin/index', AdminIndex::class . ':index');

    $app->get('/[{name}]', Index::class . ':index');


    $app->get('/admin/login', Auth::class . ':login');
    $app->post('/admin/login', Auth::class . ':loginAction');


};
