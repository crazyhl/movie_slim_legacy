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
        $this->container->db->connection()->enableQueryLog();
        // 父分类id
        $parentId = $request->getQueryParam('parentId', -1);

        $categoryQuery = CategoryModel::with(['parent']);

        $breadcrumb = [];

        if ($parentId >= 0) {
            $categoryQuery->where('parent_id', '=', $parentId);
            $category = CategoryModel::find($parentId);
            array_unshift($breadcrumb, [
                'name' => $category->name,
                'url' => $this->router->pathFor('adminCategory',[], ['parentId' => $category->id]),
                'current' => true,
            ]);
            while ($category->parent_id > 0) {
                array_unshift($breadcrumb, [
                    'name' => $category->parent->name,
                    'url' => $this->router->pathFor('adminCategory',[], ['parentId' => $category->parent->id]),
                    'current' => false,
                ]);
                $category = $category->parent;
            }
        }

        array_unshift($breadcrumb, [
            'name' => '全部分类',
            'url' => $this->router->pathFor('adminCategory'),
            'current' => count($breadcrumb) == 0 ? true : false,
        ]);

        $categories = $categoryQuery->myPaginate(3);

        $this->setTitle('分类管理');
        $log = $this->container->db->connection()->getQueryLog();
        // 生成面包屑导航

        return $this->view->render($response, 'admin/category/index.html', compact('categories', 'breadcrumb'));

    }

    public function add(Request $request, Response $response)
    {
        $categories = CategoryModel::orderBy('parent_id', 'ASC')->orderBy('order', 'ASC')->get()->toArray();
        $categories = CategoryService::groupCategory($categories);
        $categories = CategoryService::groupToTree($categories);
        return $this->view->render($response, 'admin/category/add.html', compact('categories'));
    }

    public function save(Request $request, Response $response)
    {
        $category = new CategoryModel();
        $category->name = $request->getParsedBodyParam('name');
        $category->slug = $request->getParsedBodyParam('slug');
        $category->parent_id = $request->getParsedBodyParam('parent_id');
        $category->is_show = $request->getParsedBodyParam('is_show');
        $category->order = $request->getParsedBodyParam('order');

        $category->save();

        return $response->withRedirect($this->container->router->pathFor('adminCategory'), 200);
    }
}