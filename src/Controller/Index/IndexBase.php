<?php


namespace App\Controller\Index;


use App\Controller\Base;
use App\Middleware\CustomTrait\CheckIsLogin;
use App\Model\Category;
use Psr\Container\ContainerInterface;

class IndexBase extends Base
{
    use CheckIsLogin;

    public function __construct(ContainerInterface $container)
    {
        parent::__construct($container);
        // 构造一批初始化数据
        if ($this->isLogin($container->request)) {
            $categories = Category::where('parent_id', 0)->get();
        } else {
            $categories = Category::where('parent_id', 0)->where('is_show', 1)->get();
        }

        $this->view['categories'] = $categories;
    }
}