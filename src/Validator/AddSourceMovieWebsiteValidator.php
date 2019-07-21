<?php


namespace App\Validator;

use App\Model\SourceMovieWebsite;

class AddSourceMovieWebsiteValidator extends AbstractValidator
{
    /**
     * 保存条件的数组
     * @var array
     * rules 的 key 是验证的变量名称
     * rules 的 value 是个数组
     * value 可包含 type type是声明变量的类型，可包含 string | integer | float | double | array | regex 其他的需要用到的时候在添加
     *       不同的类型会依赖后续不同的参数
     * value 包含 require 这个类型是 true | false, 如果是 true 就是必填项，如果请求参数不包含这个就会返回 false，默认 false
     * value 可包含 message 出错的提示信息，如果出错没有 message 的时候就考大家在 validation 里面自己实现默认的消息了,
     *       如果包含 type 键，这个 message 就是 type 出错的 message，如果没有 type 这个就可以是 require 为 true 的错误消息
     * value 可包含 requireMessage 这个是 require 为 true 时候的出错消息，如果这个没有，就返回默认的信息 'xxx必须填写'
     */
    public function rules()
    {
        // TODO: Implement rules() method.
        return [
            'name' => [
                'name' => '名称',
                'require' => true,
                'requireMessage' => '名称不能为空',
            ],
            'api_url' => [
                'name' => 'apiUrl',
                'require' => true,
                'requireMessage' => 'apiUrl不能为空',
                'function' => function ($apiUrl) {
                    $isUrl = filter_var($apiUrl, FILTER_VALIDATE_URL, FILTER_FLAG_PATH_REQUIRED);
                    if ($isUrl == false) {
                        return false;
                    }
                    $webSite = SourceMovieWebsite::where('api_url', '=', $apiUrl)->first();
                    if ($webSite) {
                        // 已存在就返回false
                        return false;
                    } else {
                        return true;
                    }
                },
                'functionMessage' => 'apiUrl输入不正确或已存在相同网站',
            ],
            'status' => [
                'name' => '状态',
                'require' => true,
                'requireMessage' => '必须选择状态',
                'type' => 'integer',
                'message' => '状态必须是个数字',
                'list' => [0,1],
                'listMessage' => '状态范围不正确'
            ],

        ];
    }
}
