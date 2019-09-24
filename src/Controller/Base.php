<?php

namespace App\Controller;

use Psr\Container\ContainerInterface;
use Slim\Router;
use Slim\Views\Twig;

class Base
{
    /**
     * @var ContainerInterface
     */
    protected $container;
    /**
     * @var Twig
     */
    protected $view;
    /**
     * @var Router
     */
    protected $router;

    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;

        // 初始化数据库
        $this->container->db;
        // 初始化模板默认参数
        $this->container->view['title'] = getenv('NAME');
        $this->container->view['baseTitle'] = getenv('NAME');
        $this->container->view['website'] = getenv('WEBSITE');
        // 把 view 放到 controller 里面
        $this->view = $this->container->view;
        $this->router = $this->container->router;
        // 开启服务器日志记录
        $this->container->db->connection()->enableQueryLog();
    }

    /**
     * 重设页面标题
     * @param string $title 页面标题
     * @param bool $withSuffix 是否带有后缀 就是 xxx - NAME 这种形式
     * @return $this
     */
    public function setTitle($title, $withSuffix = true)
    {
        $this->container->view['title'] = $title . ($withSuffix ? ' - ' . getenv('NAME') : '');
        $this->container->view['baseTitle'] = getenv('NAME');
        return $this;
    }


    public function display($response, $template, $data = [])
    {
        // 获取日志记录
        $queryLog = $this->container->db->connection()->getQueryLog();
        // 处理日志增加相关属性
        $useTimeMillion = 0;
        $queryCount = 0;
        foreach ($queryLog as $log) {
            $useTimeMillion += $log['time'];
            $queryCount++;
        }
        $queryLog['useTimeMillion'] = $useTimeMillion;
        $queryLog['queryCount'] = $queryCount;
        $data['queryLog'] = $queryLog;
        // 正式输出
//        echo '<pre>';
//        var_dump($data);
//        echo '</pre>';
//        exit();
        return $this->view->render($response, $template,
            $data);
    }
}