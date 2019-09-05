<?php


namespace App\Controller\Admin;


use App\Controller\Base;
use App\Model\Category as CategoryModel;
use App\Model\CronJob;
use App\Model\MovieSiteCategoryRelation;
use App\Model\SourceMovieWebsite;
use Carbon\Carbon;
use GuzzleHttp\Client;
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
        $movieWebsites = SourceMovieWebsite::myPaginate(10);

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
        $website->flag = $request->getParsedBodyParam('flag');
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
            $website->flag = $request->getParsedBodyParam('flag');
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
        if (empty($websiteId)) {
            $flashMessage = $this->container->flash;
            $flashMessage->addMessage('error', '源站不存在');
            return $response->withRedirect($this->container->router->pathFor('adminMovieWebSite'), 200);
        }
        // 获取所有分类
        $categories = CategoryModel::orderBy('parent_id', 'ASC')->orderBy('order', 'ASC')->get()->toArray();
        $categories = CategoryService::groupCategory($categories);
        $categories = CategoryService::groupToTree($categories);
        // 获取源网站分类
        $url = $website->api_url . '?ac=list';
        $client = new Client();
        $res = $client->request('GET', $url, ['verify' => false]);
        $statusCode = $res->getStatusCode();
        if ($statusCode !== 200) {
            $flashMessage = $this->container->flash;
            $flashMessage->addMessage('error', 'api获取信息失败');
            return $response->withRedirect($this->container->router->pathFor('adminMovieWebSite'), 200);
        }
        $bodyElement = new \SimpleXMLElement($res->getBody()->getContents());
        $sourceCategories = [];
        foreach ($bodyElement->class->ty as $key => $value) {
            $sourceCategories[$value['id']->__toString()] = $value->__toString();

        }
        $movieSiteCategoryRelation = MovieSiteCategoryRelation::where('source_website_id', $websiteId)->get();

        if (empty($movieSiteCategoryRelation)) {
            $movieSiteCategoryRelation = [];
        }

        $movieSiteCategoryRelationArr = [];

        foreach ($movieSiteCategoryRelation as $sourceBindRelation) {
            $movieSiteCategoryRelationArr[$sourceBindRelation['source_website_category_id']] = [
                'local_category_id' => $sourceBindRelation['local_category_id'],
                'is_show' => $sourceBindRelation['is_show'],
            ];
        }
//        echo '<pre>';
//        var_dump($sourceCategories);
//        echo '</pre>';
//        exit();
        // 获取已经绑定的分类列表
        return $this->display($response, 'admin/movie_website/bind_category.html'
            , compact('website', 'categories', 'sourceCategories', 'movieSiteCategoryRelationArr'));
    }

    public function bindCategorySave(Request $request, Response $response)
    {
        $sourceCategoryIdArr = $request->getParsedBodyParam('sourceCategoryId');
        $websiteId = $request->getParsedBodyParam('websiteId');
        // 遍历来源分类
        foreach ($sourceCategoryIdArr as $sourceCategoryId) {
            $localCategoryId = $request->getParsedBodyParam('localCategoryId-' . $sourceCategoryId);
            $isShow = $request->getParsedBodyParam('isShow-' . $sourceCategoryId);
            if ($localCategoryId === null || $isShow === null) {
                continue;
            }
            MovieSiteCategoryRelation::updateOrCreate([
                'source_website_id' => $websiteId,
                'source_website_category_id' => $sourceCategoryId,
            ], [
                'source_website_id' => $websiteId,
                'source_website_category_id' => $sourceCategoryId,
                'local_category_id' => $localCategoryId,
                'is_show' => $isShow,
            ]);
        }
        return $response->withRedirect($this->container->router->pathFor('adminMovieWebSite'), 200);
    }

    public function fullTask(Request $request, Response $response)
    {
        $flashMessage = $this->container->flash;

        $websiteId = $request->getQueryParam('id');

        $cronJob = CronJob::where([
            'name' => 'fullTask',
            'params' => json_encode([
                'webSiteId' => $websiteId,
            ]),
            'type' => 1,
        ])->first();
        if ($cronJob) {
            $flashMessage->addMessage('error', '已存在相同任务正在执行中，请不要重复加入');
        } else {
            $cronJob = new CronJob();
            $cronJob->name = 'fullTask';
            $cronJob->params = json_encode([
                'webSiteId' => $websiteId,
            ]);
            $cronJob->type = 1;
            $cronJob->execute_time = Carbon::now()->timestamp;
            $cronJob->max_execute_time = 86400 * 3; // 一个任务执行3天不过分
            $cronJob->start_time = 0;
            $cronJob->status = 0;
            $cronJob->save();

            $flashMessage->addMessage('error', '插入全部任务插入成功，不要重复插入，浪费系统资源');
        }

        return $response->withRedirect($this->container->router->pathFor('adminMovieWebSite'), 200);
    }

    public function shortTask(Request $request, Response $response)
    {
        $flashMessage = $this->container->flash;
        $websiteId = $request->getQueryParam('id');


        $cronJob = new CronJob();
        $cronJob->name = 'shortTask';
        $cronJob->params = json_encode([
            'webSiteId' => $websiteId,
        ]);
        $cronJob->type = 2;
        $cronJob->execute_time = Carbon::now()->timestamp;
        $cronJob->max_execute_time = 0;
        $cronJob->start_time = 0;
        $cronJob->status = 0;
        $cronJob->save();

        $flashMessage->addMessage('error', '定时任务插入成功，不要重复插入，浪费系统资源');

        return $response->withRedirect($this->container->router->pathFor('adminMovieWebSite'), 200);
    }


    public function test(Request $request, Response $response)
    {
        $info = \App\Service\SourceMovieWebSite::getDayMovies(1);
        echo '<pre>';
        var_dump($info);
        echo '</pre>';
        exit();
    }
}