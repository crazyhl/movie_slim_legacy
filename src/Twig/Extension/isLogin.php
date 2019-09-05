<?php


namespace App\Twig\Extension;

use App\Middleware\CustomTrait\CheckIsLogin;
use App\Utils\Val;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

/**
 * twig 模板分页扩展
 * Class Paginate
 * @package App\Twig\Extension
 */
class isLogin extends AbstractExtension
{
    use CheckIsLogin;
    public function getFunctions()
    {
        return [
            new TwigFunction('isLogin', [$this, 'checkIsLogin']),
        ];
    }

    public function checkIsLogin($isAdmin = false)
    {
        return $this->isLogin(Val::getInstance()->offsetGet('container')->request, $isAdmin);
    }
}