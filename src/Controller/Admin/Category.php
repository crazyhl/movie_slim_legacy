<?php


namespace App\Controller\Admin;


use App\Controller\Base;
use App\Model\Category as CategoryModel;
use Slim\Http\Request;
use Slim\Http\Response;

class Category extends Base
{
    /**
     * 本地列表
     * @param Request $request
     * @param Response $response
     * @return \Psr\Http\Message\ResponseInterface
     */
    public function index(Request $request, Response $response)
    {
        CategoryModel::skip(0)->take(3)->get();
        $this->setTitle('分类管理');
        return $this->view->render($response, 'admin/category/index.html');
    }
}