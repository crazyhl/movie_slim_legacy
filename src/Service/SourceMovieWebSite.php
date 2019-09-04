<?php


namespace App\Service;

use App\Model\Movie;
use App\Model\MovieSiteCategoryRelation;
use App\Model\ResourceImage;
use App\Model\SourceMovie;
use App\Model\SourceMovieWebsite as SourceMovieWebsiteModel;
use App\Utils\Val;
use Carbon\Carbon;
use GuzzleHttp\Client;

class SourceMovieWebSite
{
    public static function getDayMovies($websiteId)
    {

        $currentHour = Carbon::now()->hour;
        if ($currentHour < 4) {
            self::getMovies($websiteId, [
                'ac' => 'videolist',
            ], -5);
        }

        return self::getMovies($websiteId, [
            'ac' => 'videolist',
            'h' => 24,
        ]);
    }

    public static function getFullMovies($websiteId, $page = 1)
    {
        return self::getMovies($websiteId, [
            'ac' => 'videolist',
        ], $page);
    }

    public static function getMovies($websiteId, $params, $page = 1)
    {
        $webSite = SourceMovieWebsiteModel::find($websiteId);

        if (empty($webSite) && $webSite->status != 1) {
            return 0;
        }


        $movieSiteCategoryRelation = MovieSiteCategoryRelation::with('localCategory')->where('source_website_id', $websiteId)->get();

        if ($movieSiteCategoryRelation->isEmpty()) {
            Val::getInstance()['container']->logger->error('$websiteId: ' . $websiteId . '没绑定分类不能抓取');
            return '没绑定分类不能抓取';
        }

        $movieSiteCategoryRelationArr = [];

        foreach ($movieSiteCategoryRelation as $sourceBindRelation) {
            $movieSiteCategoryRelationArr[$sourceBindRelation['source_website_category_id']] = [
                'local_category_id' => $sourceBindRelation['local_category_id'],
                'is_show' => $sourceBindRelation['is_show'],
                'category_is_show' => $sourceBindRelation['localCategory']['is_show'],
            ];
        }

        $params['pg'] = 1;

        $buildParams = http_build_query($params);
        $fullUrl = $webSite->api_url . '?' . $buildParams;
        $totalPage = self::getTotalPage($fullUrl);
        if ($totalPage === false) {
            return '获取总页数失败';
        }
        $startPage = $page;
        if ($page < 0) {
            $startPage = $totalPage + $startPage;
        }

        for ($i = $startPage; $i <= $totalPage; $i++) {
            $params['pg'] = $i;

            $buildParams = http_build_query($params);
            $fullUrl = $webSite->api_url . '?' . $buildParams;
            sleep(rand(1,3));
            self::getMovieList($fullUrl, $websiteId, $movieSiteCategoryRelationArr);
        }


        return 1; //$bodyElement['pagecount'];
    }

    private static function saveMovieInfo($data)
    {
        $carbonNow = Carbon::now();
        // 保存图片
        // 构造保存目录
        $picFileDir = '/static/image/' . $carbonNow->year . '/' . $carbonNow->month . '/';
        $fileExt = substr($data['pic'], strrpos($data['pic'], '.'));
        $picFileName = $data['source_website_id'] . '_' . $data['source_website_movie_id'] . $fileExt;

        $fullFileDir = APP_DIR . '/public' . $picFileDir;
        if (!is_dir($fullFileDir)) {
            mkdir($fullFileDir, 0755, true);
        }

        $fullPath = $fullFileDir . $picFileName;
        $savePath = $picFileDir . $picFileName;
//        var_dump($picFileDir);
//        var_dump( $fullFileDir. $picFileName);
        $downloadPicResult = false;


        // 然后就可以保存数据了
        // 先查询影片是否存在
        $movie = Movie::where('name_md5', $data['name_md5'])->first();
        if ($movie) {
            $localMovieId = $movie->id;
            // 更新最新数据
            $isSave = false;
            if ($movie->is_show != $data['is_show']) {
                $movie->is_show = $data['is_show'];
                $isSave = true;
            }
            if (Carbon::createFromTimeString($data['last'])->gt($movie->updated_at)) {
                $movie->updated_at = Carbon::createFromTimeString($data['last']);
                $isSave = true;
            }

            if ($isSave) {
                $movie->save();
            }
        } else {
            // 重试机制
//            for ($i = 0; $i < 5; $i++) {

//            if ($data['pic']) {
//                $downloadPicResult = self::downloadMovieImage($data['pic'], $fullFileDir . $picFileName . '.tmp');
//            }
//                if ($downloadPicResult) {
//                    break;
//                }
//            }

            if (Val::getInstance()['container']['settings']['downloadCover'] && $downloadPicResult) {
                // 下载成功了就查询图片咯
                $filePath = self::checkImageExist($fullPath, $savePath);
                $data['pic'] = $filePath;
            }

            // 不存在就创建
            $movie = new Movie();
            $movie->name = $data['name'];
            $movie->name_md5 = $data['name_md5'];
            $movie->category_id = $data['category_id'];
            $movie->pic = $data['pic'];
            $movie->lang = $data['lang'];
            $movie->area = $data['area'];
            $movie->year = $data['year'];
            $movie->note = $data['note'];
            $movie->actor = $data['actor'];
            $movie->director = $data['director'];
            $movie->description = $data['description'];
            $movie->is_show = $data['is_show'];
            $movie->updated_at = Carbon::createFromTimeString($data['last']);
            $movie->save();

            $localMovieId = $movie->id;
        }

        if ($localMovieId) {
            $sourceMovie = SourceMovie::where([
                'source_website_id' => $data['source_website_id'],
                'source_website_movie_id' => $data['source_website_movie_id'],
            ])->first();

            $isSave = false;

            if ($sourceMovie) {
                if (Carbon::createFromTimeString($data['last'])->gt($sourceMovie->updated_at)) {
                    $isSave = true;
                }
            } else {
                $sourceMovie = new SourceMovie();
                $isSave = true;
            }

            if ($isSave) {
                $sourceMovie->source_website_id = $data['source_website_id'];
                $sourceMovie->source_website_category_id = $data['source_website_category_id'];
                $sourceMovie->source_website_movie_id = $data['source_website_movie_id'];
                $sourceMovie->local_id = $localMovieId;
                $sourceMovie->name = $data['name'];
                $sourceMovie->name_md5 = $data['name_md5'];
                $sourceMovie->category_id = $data['category_id'];
                $sourceMovie->pic = $data['pic'];
                $sourceMovie->lang = $data['lang'];
                $sourceMovie->area = $data['area'];
                $sourceMovie->year = $data['year'];
                $sourceMovie->note = $data['note'];
                $sourceMovie->actor = $data['actor'];
                $sourceMovie->director = $data['director'];
                $sourceMovie->description = $data['description'];
                $sourceMovie->movie_list = $data['movie_list'];
                $sourceMovie->created_at = Carbon::createFromTimeString($data['last']);
                $sourceMovie->updated_at = Carbon::createFromTimeString($data['last']);
                $sourceMovie->save();
            }
        }
    }

    private static function downloadMovieImage($picUrl, $savePath)
    {
        $client = new Client();
        $res = $client->request('GET', $picUrl, ['sink' => $savePath, 'verify' => false, 'max' => 10, 'timeout' => 30, 'read_timeout' => 30, 'connect_timeout' => 30]);
        $statusCode = $res->getStatusCode();
        if ($statusCode == 200) {
            return true;
        }

        return false;
    }

    private static function checkImageExist($filePath, $savePath)
    {
        $fileMd5 = md5_file($filePath . '.tmp');
        $existFile = ResourceImage::where('file_md5', $fileMd5)->first();

        if ($existFile) {
            $savePath = $existFile->file_path;
            unlink($filePath . '.tmp');
        } else {
            $newFile = new ResourceImage();
            $newFile->file_md5 = $fileMd5;
            $newFile->file_path = $savePath;
            $newFile->save();
            rename($filePath . '.tmp', $filePath);
        }

        return $savePath;
    }

    private static function getTotalPage($apiUrl)
    {
        $client = new Client();
        $res = $client->request('GET', $apiUrl, ['verify' => false]);
        $statusCode = $res->getStatusCode();
        if ($statusCode !== 200) {
            return false;
        }

        // 获取总页数
        $bodyElement = new \SimpleXMLElement(preg_replace('#&(?=[a-z_0-9]+=)#', '&amp;',$res->getBody()->getContents()));
        $list = $bodyElement->list;
        $pageCount = $list['pagecount']->__toString();

        return $pageCount;
    }

    private static function getMovieList($apiUrl, $websiteId, $movieSiteCategoryRelationArr)
    {
        $client = new Client();
        $res = $client->request('GET', $apiUrl, ['verify' => false, 'max' => 10, 'timeout' => 30, 'read_timeout' => 30, 'connect_timeout' => 30]);
        $statusCode = $res->getStatusCode();
        if ($statusCode !== 200) {
            return false;
        }
//        Val::getInstance()['container']->logger->error('apiUrl' . $apiUrl);
        // 获取总页数
        $bodyElement = new \SimpleXMLElement(preg_replace('#&(?=[a-z_0-9]+=)#', '&amp;',$res->getBody()->getContents()));
        $list = $bodyElement->list;
        // 保存当前影片
        foreach ($list->children() as $video) {
            // 构造data
            $tid = $video->tid->__toString();
            $name = trim($video->name->__toString());
            $dd = '';
            foreach ($video->dl->children() as $dd) {
                if (substr($dd['flag'], -4) == 'm3u8') {
                    $dd .= trim($dd->__toString());
                }
            }

            if (!isset($movieSiteCategoryRelationArr[$tid])) {
                $tid = 1;
            }

            $localCategoryId = $movieSiteCategoryRelationArr[$tid]['local_category_id'];
            if ($localCategoryId) {
                $isShow = 1;
                if ($movieSiteCategoryRelationArr[$tid]['is_show'] == 0 || $movieSiteCategoryRelationArr[$tid]['category_is_show'] == 0) {
                    $isShow = 0;
                }

                $data = [
                    'source_website_id' => $websiteId,
                    'source_website_category_id' => $tid,
                    'source_website_movie_id' => trim($video->id->__toString()),
                    'name' => $name,
                    'name_md5' => md5($name),
                    'category_id' => $localCategoryId,
                    'pic' => trim($video->pic->__toString()),
                    'lang' => trim($video->lang->__toString()),
                    'area' => trim($video->area->__toString()),
                    'year' => trim($video->year->__toString()),
                    'note' => trim($video->note->__toString()),
                    'actor' => trim($video->actor->__toString()),
                    'director' => trim($video->director->__toString()),
                    'description' => trim($video->des->__toString()),
                    'movie_list' => $dd,
                    'last' => trim($video->last->__toString()),
                    'is_show' => $isShow,
                ];

                try {
                    self::saveMovieInfo($data);
                } catch (\Exception $e) {
                    Val::getInstance()['container']->logger->error(json_encode($data) . '-----' . $e->getMessage());
                }
            }

        }
    }
}