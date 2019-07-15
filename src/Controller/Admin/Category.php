<?php


namespace App\Controller\Admin;


use App\Controller\Base;
use App\Model\Category as CategoryModel;
use Slim\Http\Request;
use Slim\Http\Response;
use App\Service\Category as CategoryService;

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
        $users['totalPage'] = 17;
        $users['totalCount'] = 3000;
        return $this->view->render($response, 'admin/category/index.html', [
            'users' => $users,
        ]);

    }

    public function add(Request $request, Response $response)
    {
        $categories = CategoryModel::orderBy('parent_id', 'ASC')->orderBy('order', 'ASC')->get()->toArray();
        $categories = CategoryService::groupCategory($categories);
        echo '<pre>';
        var_dump($categories);
        echo '</pre>';
        exit();
        return $this->view->render($response, 'admin/category/add.html', compact('categories'));

    }
}