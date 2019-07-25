<?php


namespace App\Controller\Admin;


use App\Controller\Base;
use App\Model\SourceMovieWebsite;
use Slim\Http\Request;
use Slim\Http\Response;
use App\Model\Movie as MovieModel;

class Movie extends Base
{
    /**
     * 本地列表
     * @param Request $request
     * @param Response $response
     * @return \Psr\Http\Message\ResponseInterface
     */
    public function index(Request $request, Response $response)
    {
        $movies = MovieModel::with('category')->myPaginate(10);

        $this->setTitle('影片列表');

        return $this->display($response, 'admin/movie/index.html'
            , compact('movies'));
    }


    public function sourceMovieList(Request $request, Response $response)
    {
        $movieId = $request->getQueryParam('id', 0);
        // 如果分类id为空就跳转到新增
        if ($movieId == 0) {
            return $response->withRedirect($this->container->router->pathFor('adminMovie'), 200);
        }
        $movie = MovieModel::with(['sourceMovies', 'category'])->find($movieId);

        return $this->display($response, 'admin/movie/source_list.html'
            , compact('movie'));
    }
}