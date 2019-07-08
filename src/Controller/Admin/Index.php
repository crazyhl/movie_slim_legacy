<?php


namespace App\Controller\Admin;


use App\Controller\Base;
use Slim\Http\Cookies;
use Slim\Http\Request;
use Slim\Http\Response;

class Index extends Base
{
    public function index(Request $request, Response $response, $args)
    {
        // Render index view
        $this->setTitle('后台管理首页');
        return $this->view->render($response, 'admin/index.html', $args);
    }
}