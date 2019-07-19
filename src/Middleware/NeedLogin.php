<?php


namespace App\Middleware;


use App\Middleware\CustomTrait\CheckIsLogin;
use Slim\Http\Request;
use Slim\Http\Response;

class NeedLogin extends Base
{
    use CheckIsLogin;
    /**
     * Example middleware invokable class
     *
     * @param Request $request PSR7 request
     * @param Response $response PSR7 response
     * @param callable $next Next middleware
     *
     * @return \Psr\Http\Message\ResponseInterface
     */
    public function __invoke($request, $response, $next)
    {
        if (!$this->isLogin($request)) {
            return $response->withRedirect($this->container->get('router')->pathFor('adminLogin'));
        }

        // 如果有token 还需要跟 session 对比啥的
        // 已登录
        $response = $next($request, $response);

        return $response;
    }
}