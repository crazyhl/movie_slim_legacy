<?php


namespace App\Controller\Admin;


use App\Controller\Base;
use Slim\Http\Request;
use Slim\Http\Response;
use App\Model\User as UserModel;

class User extends Base
{
    /**
     * 本地列表
     * @param Request $request
     * @param Response $response
     * @return \Psr\Http\Message\ResponseInterface
     */
    public function index(Request $request, Response $response)
    {
        $users = UserModel::myPaginate(10);
        $this->setTitle('用户管理');

        return $this->display($response, 'admin/user/index.html'
            , compact('users'));
    }

    public function add(Request $request, Response $response)
    {
        return $this->display($response, 'admin/user/add.html');
    }

    public function save(Request $request, Response $response)
    {
//        $category = new CategoryModel();
//        $category->name = $request->getParsedBodyParam('name');
//        $category->slug = $request->getParsedBodyParam('slug');
//        $category->parent_id = $request->getParsedBodyParam('parent_id');
//        $category->is_show = $request->getParsedBodyParam('is_show');
//        $category->order = $request->getParsedBodyParam('order');
//
//        $category->save();

        return $response->withRedirect($this->container->router->pathFor('adminCategory'), 200);
    }

    public function edit(Request $request, Response $response)
    {
//
//        $category = CategoryModel::find($categoryId);
//
//        $categories = CategoryModel::orderBy('parent_id', 'ASC')->orderBy('order', 'ASC')->get()->toArray();
//        $categories = CategoryService::groupCategory($categories);
//        $categories = CategoryService::groupToTree($categories);
        return $this->display($response, 'admin/category/edit.html'
            , compact('category', 'categories'));
    }

    public function update(Request $request, Response $response)
    {
        $categoryId = $request->getParsedBodyParam('id', 0);
        if ($categoryId != 0) {
//            $category = CategoryModel::find($categoryId);
//
//            $category->name = $request->getParsedBodyParam('name');
//            $category->slug = $request->getParsedBodyParam('slug');
//            $category->parent_id = $request->getParsedBodyParam('parent_id');
//            $category->is_show = $request->getParsedBodyParam('is_show');
//            $category->order = $request->getParsedBodyParam('order');
//
//            $category->save();
        }

        return $response->withRedirect($this->container->router->pathFor('adminCategory'), 200);
    }

    public function delete(Request $request, Response $response)
    {
//        $categoryId = $request->getParsedBodyParam('id', 0);
//        CategoryModel::where('id', $categoryId)->delete();

        return $response->withRedirect($this->container->router->pathFor('adminCategory'), 200);
    }
}