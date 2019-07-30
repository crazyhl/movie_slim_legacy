<?php


namespace App\Controller\Index;


use App\Controller\Base;
use App\Model\Movie;
use GuzzleHttp\Client;
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
            $movies = Movie::where('name', 'like', '%' . $keywords . '%')->where('is_show', 1)->get();
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

        $movie = Movie::with(['sourceMovies', 'category'])->where('id', $id)->where('is_show', 1)->first();

        if (empty($movie)) {
            return $response->withRedirect($this->container->router->pathFor('index'), 200);
        }
        // Render index view
        return $this->view->render($response, 'index/detail.html', compact('movie'));
    }

    public function test(Request $request, Response $response, $args)
    {
        echo "#EXTM3U
#EXT-X-VERSION:3
#EXT-X-TARGETDURATION:8
#EXT-X-MEDIA-SEQUENCE:0
#EXTINF:5.920000,
http://movie_slim_legacy.test/test2?url=https://tudou.com-l-tudou.com/20190730/21446_5dd90dce/1000k/hls/87b440ee853000000.ts
#EXTINF:3.520000,
http://movie_slim_legacy.test/test2?url=https://tudou.com-l-tudou.com/20190730/21446_5dd90dce/1000k/hls/87b440ee853000001.ts
#EXTINF:3.360000,
http://movie_slim_legacy.test/test2?url=https://tudou.com-l-tudou.com/20190730/21446_5dd90dce/1000k/hls/87b440ee853000002.ts
#EXTINF:4.000000,
http://movie_slim_legacy.test/test2?url=https://tudou.com-l-tudou.com/20190730/21446_5dd90dce/1000k/hls/87b440ee853000003.ts
#EXTINF:4.000000,
http://movie_slim_legacy.test/test2?url=https://tudou.com-l-tudou.com/20190730/21446_5dd90dce/1000k/hls/87b440ee853000004.ts
#EXTINF:4.000000,
http://movie_slim_legacy.test/test2?url=https://tudou.com-l-tudou.com/20190730/21446_5dd90dce/1000k/hls/87b440ee853000005.ts
#EXTINF:4.000000,
http://movie_slim_legacy.test/test2?url=https://tudou.com-l-tudou.com/20190730/21446_5dd90dce/1000k/hls/87b440ee853000006.ts
#EXTINF:4.000000,
http://movie_slim_legacy.test/test2?url=https://tudou.com-l-tudou.com/20190730/21446_5dd90dce/1000k/hls/87b440ee853000007.ts
#EXTINF:4.000000,
http://movie_slim_legacy.test/test2?url=https://tudou.com-l-tudou.com/20190730/21446_5dd90dce/1000k/hls/87b440ee853000008.ts
#EXTINF:3.280000,
http://movie_slim_legacy.test/test2?url=https://tudou.com-l-tudou.com/20190730/21446_5dd90dce/1000k/hls/87b440ee853000009.ts
#EXTINF:4.240000,
http://movie_slim_legacy.test/test2?url=https://tudou.com-l-tudou.com/20190730/21446_5dd90dce/1000k/hls/87b440ee853000010.ts
#EXTINF:4.440000,
http://movie_slim_legacy.test/test2?url=https://tudou.com-l-tudou.com/20190730/21446_5dd90dce/1000k/hls/87b440ee853000011.ts
#EXTINF:3.240000,
http://movie_slim_legacy.test/test2?url=https://tudou.com-l-tudou.com/20190730/21446_5dd90dce/1000k/hls/87b440ee853000012.ts
#EXTINF:4.520000,
http://movie_slim_legacy.test/test2?url=https://tudou.com-l-tudou.com/20190730/21446_5dd90dce/1000k/hls/87b440ee853000013.ts
#EXTINF:3.880000,
http://movie_slim_legacy.test/test2?url=https://tudou.com-l-tudou.com/20190730/21446_5dd90dce/1000k/hls/87b440ee853000014.ts
#EXTINF:7.080000,
http://movie_slim_legacy.test/test2?url=https://tudou.com-l-tudou.com/20190730/21446_5dd90dce/1000k/hls/87b440ee853000015.ts
#EXTINF:2.120000,
http://movie_slim_legacy.test/test2?url=https://tudou.com-l-tudou.com/20190730/21446_5dd90dce/1000k/hls/87b440ee853000016.ts
#EXTINF:4.880000,
http://movie_slim_legacy.test/test2?url=https://tudou.com-l-tudou.com/20190730/21446_5dd90dce/1000k/hls/87b440ee853000017.ts
#EXTINF:4.000000,
http://movie_slim_legacy.test/test2?url=https://tudou.com-l-tudou.com/20190730/21446_5dd90dce/1000k/hls/87b440ee853000018.ts
#EXTINF:4.000000,
http://movie_slim_legacy.test/test2?url=https://tudou.com-l-tudou.com/20190730/21446_5dd90dce/1000k/hls/87b440ee853000019.ts
#EXTINF:2.000000,
http://movie_slim_legacy.test/test2?url=https://tudou.com-l-tudou.com/20190730/21446_5dd90dce/1000k/hls/87b440ee853000020.ts
#EXTINF:4.440000,
http://movie_slim_legacy.test/test2?url=https://tudou.com-l-tudou.com/20190730/21446_5dd90dce/1000k/hls/87b440ee853000021.ts
#EXTINF:5.400000,
http://movie_slim_legacy.test/test2?url=https://tudou.com-l-tudou.com/20190730/21446_5dd90dce/1000k/hls/87b440ee853000022.ts
#EXTINF:4.440000,
http://movie_slim_legacy.test/test2?url=https://tudou.com-l-tudou.com/20190730/21446_5dd90dce/1000k/hls/87b440ee853000023.ts
#EXTINF:3.120000,
http://movie_slim_legacy.test/test2?url=https://tudou.com-l-tudou.com/20190730/21446_5dd90dce/1000k/hls/87b440ee853000024.ts
#EXTINF:2.400000,
http://movie_slim_legacy.test/test2?url=https://tudou.com-l-tudou.com/20190730/21446_5dd90dce/1000k/hls/87b440ee853000025.ts
#EXTINF:4.480000,
http://movie_slim_legacy.test/test2?url=https://tudou.com-l-tudou.com/20190730/21446_5dd90dce/1000k/hls/87b440ee853000026.ts
#EXTINF:4.000000,
http://movie_slim_legacy.test/test2?url=https://tudou.com-l-tudou.com/20190730/21446_5dd90dce/1000k/hls/87b440ee853000027.ts
#EXTINF:3.400000,
http://movie_slim_legacy.test/test2?url=https://tudou.com-l-tudou.com/20190730/21446_5dd90dce/1000k/hls/87b440ee853000028.ts
#EXTINF:5.680000,
http://movie_slim_legacy.test/test2?url=https://tudou.com-l-tudou.com/20190730/21446_5dd90dce/1000k/hls/87b440ee853000029.ts
#EXTINF:4.480000,
http://movie_slim_legacy.test/test2?url=https://tudou.com-l-tudou.com/20190730/21446_5dd90dce/1000k/hls/87b440ee853000030.ts
#EXTINF:2.280000,
http://movie_slim_legacy.test/test2?url=https://tudou.com-l-tudou.com/20190730/21446_5dd90dce/1000k/hls/87b440ee853000031.ts
#EXTINF:3.640000,
http://movie_slim_legacy.test/test2?url=https://tudou.com-l-tudou.com/20190730/21446_5dd90dce/1000k/hls/87b440ee853000032.ts
#EXTINF:4.520000,
http://movie_slim_legacy.test/test2?url=https://tudou.com-l-tudou.com/20190730/21446_5dd90dce/1000k/hls/87b440ee853000033.ts
#EXTINF:5.640000,
http://movie_slim_legacy.test/test2?url=https://tudou.com-l-tudou.com/20190730/21446_5dd90dce/1000k/hls/87b440ee853000034.ts
#EXTINF:3.880000,
http://movie_slim_legacy.test/test2?url=https://tudou.com-l-tudou.com/20190730/21446_5dd90dce/1000k/hls/87b440ee853000035.ts
#EXTINF:5.280000,
http://movie_slim_legacy.test/test2?url=https://tudou.com-l-tudou.com/20190730/21446_5dd90dce/1000k/hls/87b440ee853000036.ts
#EXTINF:4.000000,
http://movie_slim_legacy.test/test2?url=https://tudou.com-l-tudou.com/20190730/21446_5dd90dce/1000k/hls/87b440ee853000037.ts
#EXTINF:4.200000,
http://movie_slim_legacy.test/test2?url=https://tudou.com-l-tudou.com/20190730/21446_5dd90dce/1000k/hls/87b440ee853000038.ts
#EXTINF:0.720000,
http://movie_slim_legacy.test/test2?url=https://tudou.com-l-tudou.com/20190730/21446_5dd90dce/1000k/hls/87b440ee853000039.ts
#EXTINF:4.000000,
http://movie_slim_legacy.test/test2?url=https://tudou.com-l-tudou.com/20190730/21446_5dd90dce/1000k/hls/87b440ee853000040.ts
#EXTINF:5.760000,
http://movie_slim_legacy.test/test2?url=https://tudou.com-l-tudou.com/20190730/21446_5dd90dce/1000k/hls/87b440ee853000041.ts
#EXTINF:2.000000,
http://movie_slim_legacy.test/test2?url=https://tudou.com-l-tudou.com/20190730/21446_5dd90dce/1000k/hls/87b440ee853000042.ts
#EXTINF:5.120000,
http://movie_slim_legacy.test/test2?url=https://tudou.com-l-tudou.com/20190730/21446_5dd90dce/1000k/hls/87b440ee853000043.ts
#EXTINF:3.520000,
http://movie_slim_legacy.test/test2?url=https://tudou.com-l-tudou.com/20190730/21446_5dd90dce/1000k/hls/87b440ee853000044.ts
#EXTINF:4.360000,
http://movie_slim_legacy.test/test2?url=https://tudou.com-l-tudou.com/20190730/21446_5dd90dce/1000k/hls/87b440ee853000045.ts
#EXTINF:2.920000,
http://movie_slim_legacy.test/test2?url=https://tudou.com-l-tudou.com/20190730/21446_5dd90dce/1000k/hls/87b440ee853000046.ts
#EXTINF:4.000000,
http://movie_slim_legacy.test/test2?url=https://tudou.com-l-tudou.com/20190730/21446_5dd90dce/1000k/hls/87b440ee853000047.ts
#EXTINF:4.000000,
http://movie_slim_legacy.test/test2?url=https://tudou.com-l-tudou.com/20190730/21446_5dd90dce/1000k/hls/87b440ee853000048.ts
#EXTINF:4.680000,
http://movie_slim_legacy.test/test2?url=https://tudou.com-l-tudou.com/20190730/21446_5dd90dce/1000k/hls/87b440ee853000049.ts
#EXTINF:3.960000,
http://movie_slim_legacy.test/test2?url=https://tudou.com-l-tudou.com/20190730/21446_5dd90dce/1000k/hls/87b440ee853000050.ts
#EXTINF:4.040000,
http://movie_slim_legacy.test/test2?url=https://tudou.com-l-tudou.com/20190730/21446_5dd90dce/1000k/hls/87b440ee853000051.ts
#EXTINF:4.400000,
http://movie_slim_legacy.test/test2?url=https://tudou.com-l-tudou.com/20190730/21446_5dd90dce/1000k/hls/87b440ee853000052.ts
#EXTINF:6.320000,
http://movie_slim_legacy.test/test2?url=https://tudou.com-l-tudou.com/20190730/21446_5dd90dce/1000k/hls/87b440ee853000053.ts
#EXTINF:2.560000,
http://movie_slim_legacy.test/test2?url=https://tudou.com-l-tudou.com/20190730/21446_5dd90dce/1000k/hls/87b440ee853000054.ts
#EXTINF:3.560000,
http://movie_slim_legacy.test/test2?url=https://tudou.com-l-tudou.com/20190730/21446_5dd90dce/1000k/hls/87b440ee853000055.ts
#EXTINF:4.000000,
http://movie_slim_legacy.test/test2?url=https://tudou.com-l-tudou.com/20190730/21446_5dd90dce/1000k/hls/87b440ee853000056.ts
#EXTINF:4.000000,
http://movie_slim_legacy.test/test2?url=https://tudou.com-l-tudou.com/20190730/21446_5dd90dce/1000k/hls/87b440ee853000057.ts
#EXTINF:3.360000,
http://movie_slim_legacy.test/test2?url=https://tudou.com-l-tudou.com/20190730/21446_5dd90dce/1000k/hls/87b440ee853000058.ts
#EXTINF:6.680000,
http://movie_slim_legacy.test/test2?url=https://tudou.com-l-tudou.com/20190730/21446_5dd90dce/1000k/hls/87b440ee853000059.ts
#EXTINF:4.000000,
http://movie_slim_legacy.test/test2?url=https://tudou.com-l-tudou.com/20190730/21446_5dd90dce/1000k/hls/87b440ee853000060.ts
#EXTINF:4.000000,
http://movie_slim_legacy.test/test2?url=https://tudou.com-l-tudou.com/20190730/21446_5dd90dce/1000k/hls/87b440ee853000061.ts
#EXTINF:4.000000,
http://movie_slim_legacy.test/test2?url=https://tudou.com-l-tudou.com/20190730/21446_5dd90dce/1000k/hls/87b440ee853000062.ts
#EXTINF:3.520000,
http://movie_slim_legacy.test/test2?url=https://tudou.com-l-tudou.com/20190730/21446_5dd90dce/1000k/hls/87b440ee853000063.ts
#EXTINF:4.000000,
http://movie_slim_legacy.test/test2?url=https://tudou.com-l-tudou.com/20190730/21446_5dd90dce/1000k/hls/87b440ee853000064.ts
#EXTINF:4.000000,
http://movie_slim_legacy.test/test2?url=https://tudou.com-l-tudou.com/20190730/21446_5dd90dce/1000k/hls/87b440ee853000065.ts
#EXTINF:4.000000,
http://movie_slim_legacy.test/test2?url=https://tudou.com-l-tudou.com/20190730/21446_5dd90dce/1000k/hls/87b440ee853000066.ts
#EXTINF:3.080000,
http://movie_slim_legacy.test/test2?url=https://tudou.com-l-tudou.com/20190730/21446_5dd90dce/1000k/hls/87b440ee853000067.ts
#EXTINF:3.400000,
http://movie_slim_legacy.test/test2?url=https://tudou.com-l-tudou.com/20190730/21446_5dd90dce/1000k/hls/87b440ee853000068.ts
#EXTINF:3.080000,
http://movie_slim_legacy.test/test2?url=https://tudou.com-l-tudou.com/20190730/21446_5dd90dce/1000k/hls/87b440ee853000069.ts
#EXTINF:4.000000,
http://movie_slim_legacy.test/test2?url=https://tudou.com-l-tudou.com/20190730/21446_5dd90dce/1000k/hls/87b440ee853000070.ts
#EXTINF:4.000000,
http://movie_slim_legacy.test/test2?url=https://tudou.com-l-tudou.com/20190730/21446_5dd90dce/1000k/hls/87b440ee853000071.ts
#EXTINF:3.440000,
http://movie_slim_legacy.test/test2?url=https://tudou.com-l-tudou.com/20190730/21446_5dd90dce/1000k/hls/87b440ee853000072.ts
#EXTINF:4.000000,
http://movie_slim_legacy.test/test2?url=https://tudou.com-l-tudou.com/20190730/21446_5dd90dce/1000k/hls/87b440ee853000073.ts
#EXTINF:4.000000,
http://movie_slim_legacy.test/test2?url=https://tudou.com-l-tudou.com/20190730/21446_5dd90dce/1000k/hls/87b440ee853000074.ts
#EXTINF:4.000000,
http://movie_slim_legacy.test/test2?url=https://tudou.com-l-tudou.com/20190730/21446_5dd90dce/1000k/hls/87b440ee853000075.ts
#EXTINF:7.640000,
http://movie_slim_legacy.test/test2?url=https://tudou.com-l-tudou.com/20190730/21446_5dd90dce/1000k/hls/87b440ee853000076.ts
#EXTINF:4.000000,
http://movie_slim_legacy.test/test2?url=https://tudou.com-l-tudou.com/20190730/21446_5dd90dce/1000k/hls/87b440ee853000077.ts
#EXTINF:2.760000,
http://movie_slim_legacy.test/test2?url=https://tudou.com-l-tudou.com/20190730/21446_5dd90dce/1000k/hls/87b440ee853000078.ts
#EXTINF:4.000000,
http://movie_slim_legacy.test/test2?url=https://tudou.com-l-tudou.com/20190730/21446_5dd90dce/1000k/hls/87b440ee853000079.ts
#EXTINF:3.320000,
http://movie_slim_legacy.test/test2?url=https://tudou.com-l-tudou.com/20190730/21446_5dd90dce/1000k/hls/87b440ee853000080.ts
#EXTINF:4.000000,
http://movie_slim_legacy.test/test2?url=https://tudou.com-l-tudou.com/20190730/21446_5dd90dce/1000k/hls/87b440ee853000081.ts
#EXTINF:4.000000,
http://movie_slim_legacy.test/test2?url=https://tudou.com-l-tudou.com/20190730/21446_5dd90dce/1000k/hls/87b440ee853000082.ts
#EXTINF:5.400000,
http://movie_slim_legacy.test/test2?url=https://tudou.com-l-tudou.com/20190730/21446_5dd90dce/1000k/hls/87b440ee853000083.ts
#EXTINF:4.000000,
http://movie_slim_legacy.test/test2?url=https://tudou.com-l-tudou.com/20190730/21446_5dd90dce/1000k/hls/87b440ee853000084.ts
#EXTINF:4.000000,
http://movie_slim_legacy.test/test2?url=https://tudou.com-l-tudou.com/20190730/21446_5dd90dce/1000k/hls/87b440ee853000085.ts
#EXTINF:4.000000,
http://movie_slim_legacy.test/test2?url=https://tudou.com-l-tudou.com/20190730/21446_5dd90dce/1000k/hls/87b440ee853000086.ts
#EXTINF:3.240000,
http://movie_slim_legacy.test/test2?url=https://tudou.com-l-tudou.com/20190730/21446_5dd90dce/1000k/hls/87b440ee853000087.ts
#EXTINF:4.000000,
http://movie_slim_legacy.test/test2?url=https://tudou.com-l-tudou.com/20190730/21446_5dd90dce/1000k/hls/87b440ee853000088.ts
#EXTINF:4.000000,
http://movie_slim_legacy.test/test2?url=https://tudou.com-l-tudou.com/20190730/21446_5dd90dce/1000k/hls/87b440ee853000089.ts
#EXTINF:4.000000,
http://movie_slim_legacy.test/test2?url=https://tudou.com-l-tudou.com/20190730/21446_5dd90dce/1000k/hls/87b440ee853000090.ts
#EXTINF:4.000000,
http://movie_slim_legacy.test/test2?url=https://tudou.com-l-tudou.com/20190730/21446_5dd90dce/1000k/hls/87b440ee853000091.ts
#EXTINF:4.000000,
http://movie_slim_legacy.test/test2?url=https://tudou.com-l-tudou.com/20190730/21446_5dd90dce/1000k/hls/87b440ee853000092.ts
#EXTINF:4.000000,
http://movie_slim_legacy.test/test2?url=https://tudou.com-l-tudou.com/20190730/21446_5dd90dce/1000k/hls/87b440ee853000093.ts
#EXTINF:4.000000,
http://movie_slim_legacy.test/test2?url=https://tudou.com-l-tudou.com/20190730/21446_5dd90dce/1000k/hls/87b440ee853000094.ts
#EXTINF:4.000000,
http://movie_slim_legacy.test/test2?url=https://tudou.com-l-tudou.com/20190730/21446_5dd90dce/1000k/hls/87b440ee853000095.ts
#EXTINF:4.000000,
http://movie_slim_legacy.test/test2?url=https://tudou.com-l-tudou.com/20190730/21446_5dd90dce/1000k/hls/87b440ee853000096.ts
#EXTINF:4.000000,
http://movie_slim_legacy.test/test2?url=https://tudou.com-l-tudou.com/20190730/21446_5dd90dce/1000k/hls/87b440ee853000097.ts
#EXTINF:4.000000,
http://movie_slim_legacy.test/test2?url=https://tudou.com-l-tudou.com/20190730/21446_5dd90dce/1000k/hls/87b440ee853000098.ts
#EXTINF:4.000000,
http://movie_slim_legacy.test/test2?url=https://tudou.com-l-tudou.com/20190730/21446_5dd90dce/1000k/hls/87b440ee853000099.ts
#EXTINF:4.000000,
http://movie_slim_legacy.test/test2?url=https://tudou.com-l-tudou.com/20190730/21446_5dd90dce/1000k/hls/87b440ee853000100.ts
#EXTINF:4.000000,
http://movie_slim_legacy.test/test2?url=https://tudou.com-l-tudou.com/20190730/21446_5dd90dce/1000k/hls/87b440ee853000101.ts
#EXTINF:1.960000,
http://movie_slim_legacy.test/test2?url=https://tudou.com-l-tudou.com/20190730/21446_5dd90dce/1000k/hls/87b440ee853000102.ts
#EXT-X-ENDLIST";
    }

    public function test2(Request $request, Response $response, $args)
    {
        $url = $request->getQueryParam('url');
        $client = new Client();
        $res = $client->request('GET', $url, ['verify' => false, 'max' => 10, 'timeout' => 30, 'read_timeout' => 30, 'connect_timeout' => 30]);
        $statusCode = $res->getStatusCode();
        if ($statusCode == 200) {
            echo $res->getBody()->getContents();
        }

    }
}