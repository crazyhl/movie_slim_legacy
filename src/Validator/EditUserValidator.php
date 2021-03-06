<?php


namespace App\Validator;



use App\Model\Category;
use App\Model\User;

class EditUserValidator extends AbstractValidator
{
    protected $model = Category::class;

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
            'username' => [
                'name' => '用户名',
                'require' => true,
                'requireMessage' => '用户名不能为空',
//                'unique' => ''
                'function' => function ($username) {
                    $uid = $this->request->getParsedBodyParam('id');
                    $user = User::where('username', '=', $username)->first();
                    if ($user && $uid != $user->id) {
                        // 已存在就返回false
                        return false;
                    } else {
                        return true;
                    }
                },
                'functionMessage' => '已存在相同别名的分类',
            ],
            'password' => [
                'name' => '密码',
                'require' => true,
                'requireMessage' => '密码不能为空',
            ],
        ];
    }
}
