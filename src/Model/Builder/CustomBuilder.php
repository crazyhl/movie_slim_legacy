<?php

namespace App\Model\Builder;

use App\Utils\Paginate;
use Illuminate\Database\Eloquent\Builder;

class CustomBuilder extends Builder
{

    public function myPaginate($perPage = 15, $columns = ['*'])
    {
        // 获取页码
        $page = Paginate::getPageNum();
        // 调用 builder 自带的方法获取总条数
        $results = ($total = $this->toBase()->getCountForPagination())
            ? $this->forPage($page, $perPage)->get($columns)
            : $this->model->newCollection();

        $totalPage = $total ? ceil($total / $page) : 0;

        $path = $basePath = Paginate::getBasePath();
        $queryParams = Paginate::getQueryParams();
        unset($queryParams['page']);

        $queryStr = http_build_query($queryParams);
        if ($queryStr) {
            $path .= '?' . $queryStr;
        }

        return [
            'currentPage' => $page,
            'perPage' => $perPage,
            'totalCount' => $total,
            'totalPage' => $totalPage,
            'data' => $results,
            'basePath' => $basePath,
            'queryStr' => $queryStr,
            'path' => $path,
        ];
    }
}