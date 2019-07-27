<?php


namespace App\Controller\Index;

use App\Controller\Base;
use App\Model\User;
use Slim\Http\Cookies;
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
    public function login(Request $request, Response $response)
    {
        $this->setTitle('登录');

        return $this->view->render($response, 'index/login.html', [
            'formTitle' => '登录',
        ]);
    }

    /**
     * 执行登录的操作
     * @param Request $request
     * @param Response $response
     * @return mixed
     */
    public function loginAction(Request $request, Response $response)
    {
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

                // 如果需要重新hash 则重新设置一下 密码 hash
                if (password_needs_rehash($user->password, PASSWORD_DEFAULT)) {
                    $user->password = password_hash($password, PASSWORD_DEFAULT);
                }

                // 长期保存把 token 放到数据库中
                $user->token = $token;
                // 通过 remember 判定是否长期留存用户cookie
                if (strtolower($isRemember) == 'on') {
                    // 一年有效期
                    $expire = time() + 86400 * 365;
                }

                // cookie 设置 token
                $ivLen = openssl_cipher_iv_length(getenv('CRYPT_METHOD'));
                $iv = openssl_random_pseudo_bytes($ivLen);
                $cipherTextRaw = openssl_encrypt($token, getenv('CRYPT_METHOD'), getenv('APP_KEY'), OPENSSL_RAW_DATA, $iv);
                $hmac = hash_hmac('sha256', $cipherTextRaw, getenv('APP_KEY'), true);
                $cipherText = base64_encode($iv . $hmac . $cipherTextRaw);

                $cookies = new Cookies();
                $cookies->set('token', [
                    'value' => $cipherText,
                    'expires' => $expire,
                    'path' => '/',
                ]);

                $_SESSION['uid'] = $user->id;
                $_SESSION['user'] = $user;

                $user->save();
                return $response->withRedirect($this->container->get('router')->pathFor('index'))->withHeader('Set-Cookie', $cookies->toHeaders());
            } else {
                $error = '密码不正确';
            }
        } else {
            $error = '用户不存在';
        }

        return $this->view->render($response, 'login.html', [
            'formTitle' => '登录',
            'error' => $error,
            'isRemember' => $isRemember,
            'oldUsername' => $username,
        ]);
    }

    /**
     * 登出
     * @param Request $request
     * @param Response $response
     * @return Response
     */
    public function logout(Request $request, Response $response)
    {
        session_unset();
        $header = $request->getHeader('cookie');
        $cookies = Cookies::parseHeader($header);
        $cookies = new Cookies($cookies);
        $cookies->set('token', [
            'value' => null,
            'expires' => -36000,
            'path' => '/',
        ]);
        return $response->withRedirect($this->container->get('router')->pathFor('adminLogin'))
            ->withHeader('Set-Cookie', $cookies->toHeaders());
    }
}