<?php


namespace App\Middleware;


use App\Model\User;
use Slim\Http\Cookies;
use Slim\Http\Request;
use Slim\Http\Response;

class NeedLogin extends Base
{
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
        $header = $request->getHeader('cookie');
        $cookies = Cookies::parseHeader($header);

        if (empty($cookies['token'])) {
            // 未登录
            return $response->withRedirect($this->container->get('router')->pathFor('adminLogin'));
        }
        // 已登录
        $response = $next($request, $response);

        return $response;
    }
}