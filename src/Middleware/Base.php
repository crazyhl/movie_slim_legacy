<?php


namespace App\Middleware;


use Psr\Container\ContainerInterface;

class Base
{
    protected $container;

    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
        // 初始化数据库
        $this->container->db;
    }
}