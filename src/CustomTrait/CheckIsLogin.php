<?php


namespace App\CustomTrait;


use App\Model\User;
use Slim\Http\Cookies;
use Slim\Http\Request;

trait CheckIsLogin
{
    public function isLogin(Request $request){
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
            $loginUser = User::where('token', $loginToken)->first();
            if ($_SESSION['uid'] == $uid || (empty($_SESSION['uid']) && $loginUser)) {
                // 已登录
                if (empty($_SESSION['uid'])) {
                    // 如果是 remember 则要初始化 session
                    $_SESSION['uid'] = $loginUser->id;
                    $_SESSION['user'] = $loginUser;
                }
                return true;
            }

            return false;
        }

        return false;
    }
}