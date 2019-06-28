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
        $this->container->view['title'] = getenv('NAME');
        $this->container->view['website'] = getenv('WEBSITE');
        // 把 view 放到 controller 里面
        $this->view = $this->container->view;
    }

    /**
     * 重设页面标题
     * @param string $title 页面标题
     * @param bool $withSuffix 是否带有后缀 就是 xxx - NAME 这种形式
     * @return $this
     */
    public function setTitle($title, $withSuffix = true) {
        $this->container->view['title'] = $title . ($withSuffix ? ' - ' . getenv('NAME') : '');
        return $this;
    }
}