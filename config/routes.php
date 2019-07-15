<?php

use App\Controller\Admin\Auth;
use App\Controller\Admin\Category;
use App\Controller\Index;
use App\Middleware\AlreadyLogin;
use App\Middleware\NeedLogin;
use App\Middleware\Validate;
use App\Validator\AddCategoryValidator;
use Slim\App;
use App\Controller\Admin\Index as AdminIndex;

return function (App $app) {
    $container = $app->getContainer();


    $app->get('/admin/login', Auth::class . ':login')->setName('adminLogin')->add(new AlreadyLogin($container));
    $app->post('/admin/login', Auth::class . ':loginAction')->setName('adminLoginAction');

    $app->group('/admin', function (App $app) {
        $app->get('', AdminIndex::class . ':index')->setName('admin');
        $app->get('/index', AdminIndex::class . ':index')->setName('adminIndex');
        $app->get('/logout', Auth::class . ':logout')->setName('adminLogout');
        $app->group('/category', function (App $app) {
            $container = $app->getContainer();
            $app->get('', Category::class . ':index')->setName('adminCategory');
            $app->get('/add', Category::class . ':add')->setName('adminCategoryAdd');
            $app->post('/save', Category::class . ':save')->setName('adminCategorySave')
                ->add(new Validate($container, new AddCategoryValidator()));

        });
    })->add(new NeedLogin($container));
};
