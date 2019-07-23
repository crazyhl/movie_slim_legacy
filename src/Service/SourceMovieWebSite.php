<?php


namespace App\Service;

use App\Model\Movie;
use App\Model\MovieSiteCategoryRelation;
use App\Model\ResourceImage;
use App\Model\SourceMovie;
use App\Model\SourceMovieWebsite as SourceMovieWebsiteModel;
use Carbon\Carbon;
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

        $movieSiteCategoryRelation = MovieSiteCategoryRelation::with('localCategory')->where('source_website_id', $websiteId)->get();


        if (empty($movieSiteCategoryRelation)) {
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

            $isShow = 1;
            if ($movieSiteCategoryRelationArr[$tid]['is_show'] == 0 || $movieSiteCategoryRelationArr[$tid]['category_is_show'] == 0) {
                $isShow = 0;
            }

            $data = [
                'source_website_id' => $websiteId,
                'source_website_category_id' => $tid,
                'source_website_movie_id' => $video->id->__toString(),
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
                'is_show' => $isShow,
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
        $carbonNow = Carbon::now();
        // 保存图片
        // 构造保存目录
        $picFileDir = '/static/image/' . $carbonNow->year . '/' . $carbonNow->month . '/';
        $fileExt = substr($data['pic'], strrpos($data['pic'], '.'));
        $picFileName = $data['source_website_id'] . '_' . $data['source_website_movie_id'] . $fileExt;

        $fullFileDir = RUN_SCRIPT_DIR . $picFileDir;
        mkdir($fullFileDir, 0755, true);

        $fullPath = $fullFileDir . $picFileName;
        $savePath = $picFileDir . $picFileName;
//        var_dump($picFileDir);
//        var_dump( $fullFileDir. $picFileName);
        $downloadPicResult = false;
        // 重试机制
        for ($i = 0; $i < 5; $i++) {
            $downloadPicResult = self::downloadMovieImage($data['pic'], $fullFileDir . $picFileName . '.tmp');
            if ($downloadPicResult) {
                break;
            }
        }

        if ($downloadPicResult) {
            // 下载成功了就查询图片咯
            $filePath = self::checkImageExist($fullPath, $savePath);
            $data['pic'] = $filePath;
        }

        // 然后就可以保存数据了
        // 先查询影片是否存在
        $movie = Movie::where('name_md5', $data['name_md5'])->first();
        if ($movie) {
            $localMovieId = $movie->id;
        } else {
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
        $res = $client->request('GET', $picUrl, ['sink' => $savePath]);
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
}