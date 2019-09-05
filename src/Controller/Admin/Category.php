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
        // 父分类id
        $parentId = $request->getQueryParam('parentId', -1);

        $categoryQuery = CategoryModel::with(['parent']);

        $breadcrumb = [];

        if ($parentId >= 0) {
            $categoryQuery->where('parent_id', '=', $parentId);
            $category = CategoryModel::find($parentId);
            array_unshift($breadcrumb, [
                'name' => $category->name,
                'url' => $this->router->pathFor('adminCategory', [], ['parentId' => $category->id]),
                'current' => true,
            ]);
            while ($category->parent_id > 0) {
                array_unshift($breadcrumb, [
                    'name' => $category->parent->name,
                    'url' => $this->router->pathFor('adminCategory', [], ['parentId' => $category->parent->id]),
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

        $categories = $categoryQuery->myPaginate(10);
        $this->setTitle('分类管理');
        // 生成面包屑导航

//        return $this->view->render($response, 'admin/category/index.html',
//            compact('categories', 'breadcrumb', 'parentId'));
        return $this->display($response, 'admin/category/index.html'
            , compact('categories', 'breadcrumb', 'parentId'));
    }

    public function add(Request $request, Response $response)
    {
        $categories = CategoryModel::where('parent_id', 0)->orderBy('order', 'ASC')->get()->toArray();

        $parentId = $request->getQueryParam('parentId', -1);

//        return $this->view->render($response, 'admin/category/add.html', compact('categories'));
        return $this->display($response, 'admin/category/add.html'
            , compact('categories', 'parentId'));
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

    public function edit(Request $request, Response $response)
    {
        $categoryId = $request->getQueryParam('id', 0);
        // 如果分类id为空就跳转到新增
        if ($categoryId == 0) {
            return $response->withRedirect($this->container->router->pathFor('adminCategoryAdd'), 200);
        }
        $category = CategoryModel::find($categoryId);

        $categories = CategoryModel::where('parent_id', 0)->orderBy('order', 'ASC')->get()->toArray();

        return $this->display($response, 'admin/category/edit.html'
            , compact('category', 'categories'));
    }

    public function update(Request $request, Response $response)
    {
        $categoryId = $request->getParsedBodyParam('id', 0);
        if ($categoryId != 0) {
            $category = CategoryModel::find($categoryId);

            $category->name = $request->getParsedBodyParam('name');
            $category->slug = $request->getParsedBodyParam('slug');
            $category->parent_id = $request->getParsedBodyParam('parent_id');
            $category->is_show = $request->getParsedBodyParam('is_show');
            $category->order = $request->getParsedBodyParam('order');

            $category->save();
            // 同步 到movie
            \App\Model\Movie::where('category_id', $categoryId)->update(['is_show' => $category->is_show]);
        }

        return $response->withRedirect($this->container->router->pathFor('adminCategory'), 200);
    }

    public function delete(Request $request, Response $response)
    {
        $categoryId = $request->getParsedBodyParam('id', 0);
        CategoryModel::where('id', $categoryId)->delete();

        return $response->withRedirect($this->container->router->pathFor('adminCategory'), 200);
    }
}