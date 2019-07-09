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
//        $this->container->db->connection()->enableQueryLog();
        $users = CategoryModel::myPaginate(15);
        $this->setTitle('分类管理');
//        $log = $this->container->db->connection()->getQueryLog();
        return $this->view->render($response, 'admin/category/index.html', [
            'users' => $users,
        ]);

    }
}