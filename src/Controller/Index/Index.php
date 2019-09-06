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

        if ($categoryId > 0) {

        } else {
            $this->getIndexData($request);
        }

        return $this->view->render($response, 'index/index.html', $args);
    }

    public function search(Request $request, Response $response, $args)
    {
        $this->view['activeNav'] = 'home';
        $keywords = $request->getParsedBodyParam('word');
        $movies = [];
        if ($keywords) {
            $movieQuery = Movie::where('name', 'like', '%' . $keywords . '%');
            if (!$this->isLogin($request)) {
                $movieQuery->where('is_show', 1);
            }
            $movies = $movieQuery->get();
        }
        // Render index view
        return $this->view->render($response, 'index/index.html', compact('movies'));
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
}