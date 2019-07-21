<?php


namespace App\Controller\Admin;


use App\Controller\Base;
use App\Model\Category as CategoryModel;
use App\Model\SourceMovieWebsite;
use Slim\Http\Request;
use Slim\Http\Response;
use App\Service\Category as CategoryService;

class MovieWebsite extends Base
{
    /**
     * 本地列表
     * @param Request $request
     * @param Response $response
     * @return \Psr\Http\Message\ResponseInterface
     */
    public function index(Request $request, Response $response)
    {
        $movieWebsites = SourceMovieWebsite::myPaginate(3);

        $this->setTitle('影视源网站管理');

        return $this->display($response, 'admin/movie_website/index.html'
            , compact('movieWebsites'));
    }

    public function add(Request $request, Response $response)
    {
        return $this->display($response, 'admin/movie_website/add.html');
    }

    public function save(Request $request, Response $response)
    {
        $website = new SourceMovieWebsite();
        $website->name = $request->getParsedBodyParam('name');
        $website->api_url = $request->getParsedBodyParam('api_url');
        $website->status = $request->getParsedBodyParam('status');

        $website->save();

        return $response->withRedirect($this->container->router->pathFor('adminMovieWebSite'), 200);
    }

    public function edit(Request $request, Response $response)
    {
        $websiteId = $request->getQueryParam('id', 0);
        // 如果分类id为空就跳转到新增
        if ($websiteId == 0) {
            return $response->withRedirect($this->container->router->pathFor('adminCategoryAdd'), 200);
        }
        $website = SourceMovieWebsite::find($websiteId);
        return $this->display($response, 'admin/movie_website/edit.html'
            , compact('website'));
    }

    public function update(Request $request, Response $response)
    {
        $websiteId = $request->getParsedBodyParam('id', 0);
        if ($websiteId != 0) {
            $website = SourceMovieWebsite::find($websiteId);

            $website->name = $request->getParsedBodyParam('name');
            $website->api_url = $request->getParsedBodyParam('api_url');
            $website->status = $request->getParsedBodyParam('status');

            $website->save();
        }

        return $response->withRedirect($this->container->router->pathFor('adminMovieWebSite'), 200);
    }

    public function softDelete(Request $request, Response $response)
    {
        $websiteId = $request->getQueryParam('id', 0);
        $website = SourceMovieWebsite::find($websiteId);
        if ($website) {
            $website->status = 0;
            $website->save();
        }


        return $response->withRedirect($this->container->router->pathFor('adminMovieWebSite'), 200);
    }

    public function bindCategory(Request $request, Response $response)
    {
        $websiteId = $request->getQueryParam('id', 0);
        $website = SourceMovieWebsite::find($websiteId);
        if ($website) {
            $website->status = 0;
            $website->save();
        }


        return $response->withRedirect($this->container->router->pathFor('adminMovieWebSite'), 200);
    }
}