<?php


namespace App\Service;

use App\Model\MovieSiteCategoryRelation;
use App\Model\SourceMovieWebsite as SourceMovieWebsiteModel;
use GuzzleHttp\Client;

class SourceMovieWebSite
{
    public static function getFullMovies($websiteId)
    {
        return self::getMovies($websiteId, [
            'ac' => 'videolist',
        ]);
    }

    public static function getMovies($websiteId, $params, $page = 1)
    {
        $webSite = SourceMovieWebsiteModel::find($websiteId);

        if (empty($webSite) && $webSite->status != 1) {
            return 0;
        }

        $params['pg'] = $page;

        $params = http_build_query($params);
        $fullUrl = $webSite->api_url . '?' . $params;

        $client = new Client();
        $res = $client->request('GET', $fullUrl);
        $statusCode = $res->getStatusCode();
        if ($statusCode !== 200) {
            return '获取源站数据失败';
        }

        $movieSiteCategoryRelation = MovieSiteCategoryRelation::where('source_website_id', $websiteId)->get();

        if (empty($movieSiteCategoryRelation)) {
            return '没绑定分类不能抓取';
        }

        $movieSiteCategoryRelationArr = [];

        foreach ($movieSiteCategoryRelation as $sourceBindRelation) {
            $movieSiteCategoryRelationArr[$sourceBindRelation['source_website_category_id']] = [
                'local_category_id' => $sourceBindRelation['local_category_id'],
                'is_show' => $sourceBindRelation['is_show'],
            ];
        }


        $bodyElement = new \SimpleXMLElement($res->getBody()->getContents());
        $list = $bodyElement->list;
        $pageCount = $list['pagecount']->__toString();
        // 保存当前影片
        foreach ($list->children() as $video) {
            // 构造data
            $tid = $video->tid->__toString();
            $name = $video->name->__toString();
            $dd = '';
            foreach ($video->dl->children() as $dd) {
                if (substr($dd['flag'], -4) == 'm3u8') {
                    $dd = $dd->__toString();
                }
            }
            $data = [
                'source_website_id' => $websiteId,
                'source_website_category_id' => $tid,
                'name' => $name,
                'name_md5' => md5($name),
                'category_id' => $movieSiteCategoryRelationArr[$tid]['local_category_id'],
                'pic' => $video->pic->__toString(),
                'lang' => $video->lang->__toString(),
                'area' => $video->area->__toString(),
                'year' => $video->year->__toString(),
                'note' => $video->note->__toString(),
                'actor' => $video->actor->__toString(),
                'director' => $video->director->__toString(),
                'description' => $video->des->__toString(),
                'movie_list' => $dd,
                'last' => $video->last->__toString(),
            ];

            self::saveMovieInfo($data);
        }
        // 加载其他页面
//        if ($page < $pageCount) {
//            $page += 1;
//            self::getMovies($websiteId, $params, $page);
//        }
        return 1; //$bodyElement['pagecount'];
    }

    private static function saveMovieInfo($data)
    {
        // 用文件md5 来当做图片的路径好了
    }
}