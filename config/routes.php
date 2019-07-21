<?php

use App\Controller\Admin\Auth;
use App\Controller\Admin\Category;
use App\Controller\Admin\MovieWebsite;
use App\Middleware\AlreadyLogin;
use App\Middleware\NeedLogin;
use App\Middleware\Validate;
use App\Validator\AddCategoryValidator;
use App\Validator\AddSourceMovieWebsiteValidator;
use App\Validator\DeleteCategoryValidator;
use App\Validator\EditCategoryValidator;
use App\Validator\EditSourceMovieWebsiteValidator;
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
            $app->get('/edit', Category::class . ':edit')->setName('adminCategoryEdit');
            $app->post('/save', Category::class . ':save')->setName('adminCategorySave')
                ->add(new Validate($container, new AddCategoryValidator()));

            $app->post('/update', Category::class . ':update')->setName('adminCategoryUpdate')
                ->add(new Validate($container, new EditCategoryValidator()));
            $app->delete('delete', Category::class . ':delete')->setName('adminCategoryDelete')
                ->add(new Validate($container, new DeleteCategoryValidator()));
        });

        $app->group('/movie-website', function (App $app) {
            $container = $app->getContainer();
            $app->get('', MovieWebsite::class . ':index')->setName('adminMovieWebSite');
            $app->get('/add', MovieWebsite::class . ':add')->setName('adminMovieWebSiteAdd');
            $app->get('/edit', MovieWebsite::class . ':edit')->setName('adminMovieWebSiteEdit');
            $app->post('/save', MovieWebsite::class . ':save')->setName('adminMovieWebSiteSave')
                ->add(new Validate($container, new AddSourceMovieWebsiteValidator()));
            $app->post('/update', MovieWebsite::class . ':update')->setName('adminMovieWebSiteUpdate')
                ->add(new Validate($container, new EditSourceMovieWebsiteValidator()));
            $app->get('/delete', MovieWebsite::class . ':softDelete')->setName('adminMovieWebSiteDelete');
            $app->get('/bind-category', MovieWebsite::class . ':bindCategory')
                ->setName('adminMovieWebSiteBindCategory');
        });
    })->add(new NeedLogin($container));
};
