<?php


namespace App\Middleware\CustomTrait;


use App\Model\User;
use Slim\Http\Cookies;
use Slim\Http\Request;

trait CheckIsLogin
{
    public function isLogin(Request $request, $isAdmin = false)
    {
        $header = $request->getHeader('cookie');
        $cookies = Cookies::parseHeader($header);

        if ($cookies['token']) {
            // 有已登录数据，校验数据是否正确
            $cipherText = base64_decode($cookies['token']);
            $ivLen = openssl_cipher_iv_length(getenv('CRYPT_METHOD'));
            $iv = substr($cipherText, 0, $ivLen);
            $sha2Len = 32;
            $cipherTextRaw = substr($cipherText, $ivLen + $sha2Len);
            $loginToken = openssl_decrypt($cipherTextRaw, getenv('CRYPT_METHOD'), getenv('APP_KEY'), OPENSSL_RAW_DATA, $iv);
            list($uid,) = explode('-', $loginToken);
            $loginUserQuery = User::where('token', $loginToken);
            if ($isAdmin) {
                $loginUserQuery->where('is_admin', 1);
            }
            $loginUser = $loginUserQuery->first();

            $isLoginCondition = ($loginUser && $_SESSION['uid'] == $uid);

            if ($isAdmin) {
                $isLoginCondition = ($isLoginCondition && $loginUser->is_admin == 1);
            }

            if ($isLoginCondition) {
                // 已登录
                $_SESSION['uid'] = $loginUser->id;
                $_SESSION['user'] = $loginUser;
                return true;
            }

            return false;
        }

        return false;
    }
}