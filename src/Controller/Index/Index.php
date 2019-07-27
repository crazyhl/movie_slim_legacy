<?php


namespace App\Controller\Index;


use App\Controller\Base;
use Slim\Http\Request;
use Slim\Http\Response;

class Index extends Base
{
    public function index(Request $request, Response $response, $args)
    {
        // Sample log message
        $this->container->get('logger')->info("Slim-Skeleton '/' route123");

        // Render index view
        return $this->view->render($response, 'index.html', $args);
    }
}