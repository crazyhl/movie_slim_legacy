<?php


namespace App\Controller\Index;


use App\Controller\Base;
use Psr\Container\ContainerInterface;

class IndexBase extends Base
{
    public function __construct(ContainerInterface $container)
    {
        parent::__construct($container);
        // 构造一批初始化数据
    }
}