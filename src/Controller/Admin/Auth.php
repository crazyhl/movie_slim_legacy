<?php


namespace App\Controller\Admin;

use App\Controller\Base;
use App\Model\User;
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

        // 接受账号密码
//        var_dump($request->getParsedBody());
        $username = $request->getParsedBodyParam('username');
        $password = $request->getParsedBodyParam('password');
        $isRemember = $request->getParsedBodyParam('remember');

        $error = '';
        // 通过用户名密码，检测用户名密码是否正确
        $user = User::where('username', $username)->first();
        if ($user) {
            if (password_verify($password, $user->password)) {
                $expire = 0;
                $requestTime = $request->getServerParam('REQUEST_TIME', time());
                $uid = $user->id;
                $token = $uid . '-' . $requestTime;

                $needUpdateDb = false;
                // 如果需要重新hash 则重新设置一下 密码 hash
                if (password_needs_rehash($user->password, PASSWORD_DEFAULT)) {
                    $user->password = password_hash($password, PASSWORD_DEFAULT);
                    $needUpdateDb = true;
                }
                // 通过 remember 判定是否长期留存用户cookie
                if (strtolower($isRemember) == 'on') {
                    // 长期保存把 token 放到数据库中
                    $user->token = $token;
                    $needUpdateDb = true;
                    // 一年有效期
                    $expire = time() + 86400 * 365;
                }
                // cookie 设置 token
                $ivlen = openssl_cipher_iv_length(getenv('CRYPT_METHOD'));
                $iv = openssl_random_pseudo_bytes($ivlen);
                $ciphertext = openssl_encrypt($token, getenv('CRYPT_METHOD'), getenv('APP_KEY'), 0, $iv);

                setcookie("token", $ciphertext, $expire);
                if ($needUpdateDb) {
                    $user->save();
                }

                return $response->withRedirect('/admin');
            } else {
                $error = '密码不正确';
            }
        } else {
            $error = '用户不存在';
        }

        return $this->view->render($response, 'admin/login.html', [
            'formTitle' => '后台登录',
            'error' => $error,
            'isRemember' => $isRemember,
            'oldUsername' => $username,
        ]);
    }
}