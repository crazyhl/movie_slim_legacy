<?php


namespace App\Command;


use Slim\Container;
use Symfony\Component\Console\Command\Command;

/**
 * 所有的command的基类
 * Class BaseCommand
 * @package App\Command
 */
abstract class BaseCommand extends Command
{
    /**
     * @var Container
     */
    protected $container;

    public function __construct(Container $container, $name = null)
    {
        parent::__construct($name);
        $this->container = $container;
        // 初始化数据库连接
        $this->container->db;
    }
}