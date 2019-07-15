<?php
/**
 * Created by PhpStorm.
 * User: haoliang
 * Date: 2019-03-13
 * Time: 15:20
 */

namespace App\Middleware;


use App\Controller\User;
use App\Utils\JWT;
use App\Utils\Old;
use App\Validator\AbstractValidator;
use Jose\Component\Core\Converter\StandardConverter;
use Slim\Container;
use Slim\Http\Request;
use Slim\Http\Response;
use App\Model\User as UserModel;
use Slim\Http\StatusCode;

/**
 * 检测菜单权限的中间件
 * Class CheckUrl
 * @package App\Middleware
 */
class Validate
{
    /**
     * @var AbstractValidator $validator ;
     */
    private $validator;
    /**
     * @var Container $container ;
     */
    private $container;

    public function __construct(Container $container, AbstractValidator $validator)
    {
        $this->container = $container;
        $this->validator = $validator;
    }


    /**
     * @param Request $request
     * @param Response $response
     * @param $next
     * @return Response
     */
    public function __invoke(Request $request, Response $response, $next)
    {
        $routeParams = $request->getAttribute('routeInfo')[2];

        list($result, $message) = $this->validator->validation($request, $routeParams);

        if ($result) {
            $response = $next($request, $response);
        } else {
            $old = Old::get();

            $flashMessage  =$this->container->flash;
            $flashMessage->addMessage('error', $message);
            $flashMessage->addMessage('old', $old);
            $response = $response->withRedirect($request->getHeader('referer')[0], 200);
        }


        return $response;
    }
}
