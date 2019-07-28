<?php


namespace App\Middleware;


use App\Middleware\CustomTrait\CheckIsLogin;
use Slim\Http\Request;
use Slim\Http\Response;

class AlreadyIndexLogin extends Base
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
        $referer = $request->getQueryParam('ref', $request->getHeader('HTTP_REFERER') ?: 'index');

        if ($this->isLogin($request)) {
            return $response->withRedirect($this->container->get('router')->pathFor($referer));
        }

        // 未登录
        $response = $next($request, $response);

        return $response;
    }
}