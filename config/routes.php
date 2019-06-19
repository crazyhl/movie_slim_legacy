<?php

use App\Controller\Index;
use Slim\App;

return function (App $app) {
    $container = $app->getContainer();

    $app->get('/[{name}]', Index::class . ':index');
};
