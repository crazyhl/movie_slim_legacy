<?php

namespace App\Utils;

use Slim\Http\Request;

/**
 * 分页工具类
 * Class Paginate
 * @package App\Utils
 */
class Paginate
{
    /**
     * 获取页码
     * @return int
     */
    public static function getPageNum()
    {
        $container = Val::getInstance()['container'];
        /**
         * @var $request Request
         */
        $request = $container->request;
        $page = $request->getQueryParam('page');

        if ($page === null) {
            $page = 1;
        }

        if ($page <= 0) {
            $page = 1;
        }

        return intval($page);
    }



    /**
     * 获取 basePath
     * @return string
     */
    public function getBasePath()
    {
        $container = Val::getInstance()['container'];

        /**
         * @var $request Request
         */
        $request = $container->request;

        return $request->getUri()->getPath();
    }

    public function getQueryParams()
    {
        $container = Val::getInstance()['container'];
        /**
         * @var $request Request
         */
        $request = $container->request;

        return $request->getQueryParams();
    }
}