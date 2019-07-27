<?php


namespace App\Controller\Index;


use App\Controller\Base;
use App\Model\Movie;
use Slim\Http\Request;
use Slim\Http\Response;

class Index extends Base
{
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
            $movies = Movie::where('name', 'like', '%' . $keywords . '%')->get();
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

        $movie = Movie::with(['sourceMovies', 'category'])->find($id);

        if (empty($movie)) {
            return $response->withRedirect($this->container->router->pathFor('index'), 200);
        }
        // Render index view
        return $this->view->render($response, 'index/detail.html', compact('movie'));
    }
}