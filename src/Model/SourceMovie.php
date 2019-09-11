<?php


namespace App\Model;

class SourceMovie extends Base
{
    protected $table = 'source_movie';

    public function localMovie()
    {
        return $this->belongsTo(Movie::class, 'name_md5', 'name_md5');
    }

    public function sourceWebsite()
    {
        return $this->belongsTo(SourceMovieWebsite::class, 'source_website_id', 'id');
    }

    public function getFormatMovieListAttribute($value)
    {
        $replaceArr = [
            'v-mtime.com' => 'ifeng.com-v-ifeng.com',
            'vip.okzybo.com' => 'youku.cdn-56.com',
            'youku.com-www-163.com' => 'ifeng.com-v-ifeng.com',
            'cdn.okokyun.com' => 'sohu.com-v-sohu.com',
            'cn2.okokyun.com' => 'youku.cdn1-letv.com',
            'yun.kakazy-yun.com' => 'zy.kubo-360-tudou.com',
            'zy.kakazy-yun.com' => 'zy.baidu-360-yyy-kubo.com',
            'kbzy.zxziyuan-yun.com' => 'youku.com-kubozy.com',
            '163.com-l-163.com' => 'bilibili.com-l-163.com',
            'youku.com-i-youku.com' => 'bilibili.com-l-163.com',
            '789.com-l-163.com' => 'bilibili.com-l-163.com',
            'ifeng.com-l-ifeng.com' => 'youku.com-youku.net',
            'iqiyi.com-l-iqiyi.com' => 'youku.com-iqiyi.net',
            'youku.com-okzy.com' => 'youku.com-qq.net',
            'fuli.yazyzw.com' => 'zouyu.laohu-zuida.com',
            'fuli.zuida-youku-le.com' => 'zouyu.laohu-zuida.com',
            'yingshi.yazyzw.com' => 'shenling.dijiang-zuida.com',
            'sohu.zuida-163sina.com' => 'shenling.dijiang-zuida.com',
            'youku163.zuida-bofang.com' => 'yushou.qitu-zuida.com',
            'zaixian.jingpin88.com' => 'bofang.jingpin88.com',
            'leshi.zuidameiju.com' => 'iqiyi.zuidameiju.com',
            'zouyu.laohu-zuida.com' => 'leshi.cdn-zuyida.com',
            'hao.zuida-youku.com' => 'kuku.zuida-youku.com',
            'shenling.dijiang-zuida.com' => 'yiyi.55zuiday.com',
            'iqiyi.qq-zuidazy.com' => 'feifei.feifeizuida.com',
            'videox6.lcfcyyek.com' => 'videox6.taoquan88.com',
            'videox3.ju1zhe.com' => 'videox3.zhigaogongshui.com',
            'cdn2-youku.jshuajiu.com' => 'cdn2-youku.kangliyunbao.com',
            'cdn2.pjwl888.com' => 'cdn2.nixiangwozuo.com',
            'cdn3.pjwl888.com' => 'cdn3.nixiangwozuo.com',
        ];

        $movieList = explode('#', $this->movie_list);
        $formatMovieList = [];
        foreach ($movieList as $movie) {
            $movie = trim($movie);
            if ($movie) {
                $movieInfo = explode('$', $movie);
                if (count($movieInfo > 2)) {
                    foreach ($replaceArr as $from => $to) {
                        $url = str_replace($from, $to, $movieInfo[1]);
                    }

                    if (substr($url, 0, 5) === 'http:') {
                        $url = substr($url, 5);
                    }

                    $formatMovieList[] = [
                        'name' => $movieInfo[0],
                        'url' => $url,
                    ];
                }
            }
        }
        return $formatMovieList;
    }

    public function getPicAttribute($pic)
    {
        $replaceArr = [
            'pic.china-gif.com' => 'rpg.pic-imges.com'
        ];

        foreach ($replaceArr as $from => $to) {
            $pic = str_replace($from, $to, $pic);
        }

        return $pic;
    }
}