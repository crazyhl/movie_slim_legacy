<?php


namespace App\Controller\Admin;


use App\Controller\Base;
use Slim\Http\Request;
use Slim\Http\Response;

class Index extends Base
{
    public function index(Request $request, Response $response, $args)
    {
        // Render index view
        return $this->view->render($response, 'index.html', $args);
    }
}