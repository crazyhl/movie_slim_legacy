<?php


namespace App\Controller;


use Psr\Container\ContainerInterface;

class Base
{
    protected $container;
    protected $view;

    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;

        // 初始化数据库
        $this->container->db;
        // 初始化模板默认参数
        $this->container->view['title'] = 'xx影视';
        // 把 view 放到 controller 里面
        $this->view = $this->container->view;
    }
}