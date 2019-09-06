<?php


namespace App\Controller\Index;


use App\Model\Category;
use App\Model\Movie;
use Slim\Http\Request;
use Slim\Http\Response;

class Index extends IndexBase
{
    public function index(Request $request, Response $response, $args)
    {
        // Render index view
        $this->view['activeNav'] = 'home';
        $categoryId = $request->getQueryParam('cid', 0);
        $keywords = $request->getQueryParam('word');

        if ($keywords) {
            $this->search($request);
        } else if ($categoryId > 0) {
            $this->getCategoryData($request);
        } else {
            $this->getIndexData($request);
        }

        return $this->view->render($response, 'index/index.html', $args);
    }



    public function detail(Request $request, Response $response, $args)
    {
        $this->view['activeNav'] = 'home';
        $id = $request->getQueryParam('id');
        if (empty($id)) {
            return $response->withRedirect($this->container->router->pathFor('index'), 200);
        }
        $movieQuery = Movie::with(['sourceMovies', 'category'])->where('id', $id);
        if (!$this->isLogin($request)) {
            $movieQuery->where('is_show', 1);
        }
        $movie = $movieQuery->first();

        if (empty($movie)) {
            return $response->withRedirect($this->container->router->pathFor('index'), 200);
        }

        // 构造面包屑导航
        $breadcrumb = [];
        $breadcrumb[] = [
            'name' => $movie->name,
            'url' => $this->router->pathFor('indexDetail', [], ['id' => $movie->id]),
            'current' => true,
        ];
        array_unshift($breadcrumb, [
            'name' => $movie->category->name,
            'url' => $this->router->pathFor('index', [], ['cid' => $movie->category->id]),
            'current' => false,
        ]);

        if ($movie->category->parent) {
            array_unshift($breadcrumb, [
                'name' => $movie->category->parent->name,
                'url' => $this->router->pathFor('index', [], ['cid' => $movie->category->parent->id]),
                'current' => false,
            ]);
        }
        array_unshift($breadcrumb, [
            'name' => '首页',
            'url' => $this->router->pathFor('index'),
            'current' => false,
        ]);
        $this->view['breadcrumb'] = $breadcrumb;


        // Render index view
        return $this->view->render($response, 'index/detail.html', compact('movie'));
    }

    // 获取首页数据
    private function getIndexData(Request $request)
    {
        // 构造一批初始化数据
        $isLogin = $this->isLogin($request);
        if ($isLogin) {
            $categories = Category::with('childList')->where('parent_id', 0)->get();
        } else {
            $categories = Category::with('childList')->where('parent_id', 0)->where('is_show', 1)->get();
        }

        $newestMovieListByCategory = [];
        //遍历最新
        foreach ($categories as $category) {
            $categoryIdArr = [];
            $categoryIdArr[] = $category->id;
            foreach ($category->childList as $childCategory) {
                $categoryIdArr[] = $childCategory->id;
            }

            $newestMovieListQuery = Movie::with(['category'])->whereIn('category_id', $categoryIdArr)
                ->orderBy('updated_at', 'DESC')
                ->limit(6);
            if (!$isLogin) {
                $newestMovieListQuery->where('is_show', 1);
            }

            $newestMovieListByCategory[] = [
                'category' => $category,
                'movieList' => $newestMovieListQuery->get(),
            ];

            $queryLog = $this->container->db->connection()->getQueryLog();
        }

        $this->view['newestMovieListByCategory'] = $newestMovieListByCategory;
    }

    private function getCategoryData(Request $request)
    {
        $categoryId = $request->getQueryParam('cid', 0);
        $activeNavId = $categoryId;

        $category = Category::with(['parent', 'childList'])->where('id', $categoryId)->first();
        // 构造面包屑导航
        $breadcrumb = [];
        $breadcrumb[] = [
            'name' => $category->name,
            'url' => $this->router->pathFor('index', [], ['cid' => $category->id]),
            'current' => true,
        ];
        if ($category->parent) {
            $activeNavId = $category->parent->id;
            array_unshift($breadcrumb, [
                'name' => $category->parent->name,
                'url' => $this->router->pathFor('index', [], ['cid' => $category->parent->id]),
                'current' => false,
            ]);
        }
        array_unshift($breadcrumb, [
            'name' => '首页',
            'url' => $this->router->pathFor('index'),
            'current' => false,
        ]);
        // 构造查询id
        $categoryIdArr = [];
        $categoryIdArr[] = $categoryId;
        if ($category->childList) {
            foreach ($category->childList as $childCategory) {
                $categoryIdArr[] = $childCategory->id;
            }
        }
        // 构造查询
        $movieQuery = Movie::with('category')->whereIn('category_id', $categoryIdArr)->orderBy('updated_at', 'DESC');
        if (!$this->isLogin($request)) {
            $movieQuery->where('is_show', 1);
        }

        $movieList = $movieQuery->myPaginate(18);

        $this->view['activeNav'] = $activeNavId;
        $this->view['breadcrumb'] = $breadcrumb;
        $this->view['categoryMovieList'] = $movieList;
    }

    private function search(Request $request)
    {
        $keywords = $request->getQueryParam('word');





        // 构造面包屑导航
        $breadcrumb = [];

        $breadcrumb[] = [
            'name' => '首页',
            'url' => $this->router->pathFor('index'),
            'current' => false,
        ];

        $breadcrumb[] = [
            'name' => '搜索『 ' . $keywords . '』',
            'url' => $this->router->pathFor('index', [], ['word' => $keywords]),
            'current' => true,
        ];

        // 构造查询id

        // 构造查询
        $movieQuery = Movie::where('name', 'like', '%' . $keywords . '%')->orderBy('updated_at', 'DESC');
        if (!$this->isLogin($request)) {
            $movieQuery->where('is_show', 1);
        }


        $movieList = $movieQuery->myPaginate(18);
        $this->view['activeNav'] = 'home';
        $this->view['breadcrumb'] = $breadcrumb;
        $this->view['searchMovieList'] = $movieList;
    }
}