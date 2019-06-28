<?php


namespace App\Controller\Admin;

use App\Controller\Base;
use Slim\Http\Request;
use Slim\Http\Response;

/**
 * 用户登录授权
 * Class Auth
 * @package App\Controller\Admin
 */
class Auth extends Base
{
    /**
     * 登录页面
     * @param Request $request
     * @param Response $response
     * @return mixed
     */
    public function login(Request $request, Response $response){
        $this->setTitle('后台登录');

        return $this->view->render($response, 'admin/login.html', [
            'formTitle' => '后台登录',
        ]);
    }

    /**
     * 执行登录的操作
     * @param Request $request
     * @param Response $response
     * @return mixed
     */
    public function loginAction(Request $request, Response $response){
        $this->setTitle('后台登录');

        return $this->view->render($response, 'admin/login.html', [
            'formTitle' => '后台登录',
        ]);
    }
}