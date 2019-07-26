<?php


namespace App\Controller\Admin;


use App\Controller\Base;
use App\Model\SourceMovie;
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

    public function changeField(Request $request, Response $response)
    {
        $movieId = $request->getQueryParam('id', 0);
        // 如果分类id为空就跳转到新增
        if ($movieId == 0) {
            return $response->withRedirect($this->container->router->pathFor('adminMovie'), 200);
        }
        $movie = MovieModel::find($movieId);

        if (empty($movie)) {
            return $response->withRedirect($this->container->router->pathFor('adminMovie'), 200);
        }

        $fieldName = $request->getQueryParam('field', '');
        $value = $request->getQueryParam('value', null);
        if ($fieldName && $value !== null) {
            if ($fieldName == 'is_show') {
                if ($value != 0) {
                    $value = 1;
                }
            }

            $movie->$fieldName = $value;
            $movie->save();
        }

        return $response->withRedirect($this->container->router->pathFor('adminMovieEdit', [], ['id' => $movieId]), 200);
    }

    public function useSourceInfo(Request $request, Response $response)
    {
        $sourceMovieId = $request->getQueryParam('id', 0);
        // 如果分类id为空就跳转到新增
        if ($sourceMovieId == 0) {
            return $response->withRedirect($this->container->router->pathFor('adminMovie'), 200);
        }
        $sourceMovie = SourceMovie::with('localMovie')->find($sourceMovieId);

        if (empty($sourceMovie)) {
            return $response->withRedirect($this->container->router->pathFor('adminMovie'), 200);
        }

        $fieldName = $request->getQueryParam('field', '');
        if ($fieldName) {
            $localMovie = $sourceMovie->localMovie;
            if ($localMovie) {
                $localMovie->$fieldName = $sourceMovie->$fieldName;
                $localMovie->save();
            }
        }

        return $response->withRedirect($this->container->router->pathFor('adminMovieEdit', [], ['id' => $sourceMovie->localMovie->id]), 200);
    }
}