<?php


namespace App\Controller\Index;


use App\Middleware\CustomTrait\CheckIsLogin;
use App\Model\Movie;
use Slim\Http\Request;
use Slim\Http\Response;

class Index extends IndexBase
{
    use CheckIsLogin;

    public function index(Request $request, Response $response, $args)
    {
        // Render index view
        return $this->view->render($response, 'index/index.html', $args);
    }

    public function search(Request $request, Response $response, $args)
    {
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
}