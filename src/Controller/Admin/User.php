<?php


namespace App\Controller\Admin;


use App\Controller\Base;
use Slim\Http\Request;
use Slim\Http\Response;
use App\Model\User as UserModel;

class User extends Base
{
    /**
     * 本地列表
     * @param Request $request
     * @param Response $response
     * @return \Psr\Http\Message\ResponseInterface
     */
    public function index(Request $request, Response $response)
    {
        $users = UserModel::myPaginate(10);
        $this->setTitle('用户管理');

        return $this->display($response, 'admin/user/index.html'
            , compact('users'));
    }

    public function add(Request $request, Response $response)
    {
        return $this->display($response, 'admin/user/add.html');
    }

    public function save(Request $request, Response $response)
    {
        $user = new UserModel();
        $user->username = $request->getParsedBodyParam('username');
        $user->password = password_hash($request->getParsedBodyParam('password'), PASSWORD_DEFAULT);
        $user->is_admin = 0;

        $user->save();
        return $response->withRedirect($this->container->router->pathFor('adminUser'), 200);
    }

    public function edit(Request $request, Response $response)
    {
        $uid = $request->getQueryParam('id');

        $user = UserModel::find($uid);
        return $this->display($response, 'admin/user/edit.html'
            , compact('user'));
    }

    public function update(Request $request, Response $response)
    {
        $uid = $request->getParsedBodyParam('id');
        $user = UserModel::find($uid);
        if ($user != 0) {
            $user->username = $request->getParsedBodyParam('username');
            $user->password = password_hash($request->getParsedBodyParam('password'), PASSWORD_DEFAULT);

            $user->save();
        }

        return $response->withRedirect($this->container->router->pathFor('adminUser'), 200);
    }

    public function delete(Request $request, Response $response)
    {
//        $categoryId = $request->getParsedBodyParam('id', 0);
//        CategoryModel::where('id', $categoryId)->delete();
        $uid = $request->getParsedBodyParam('id');
        UserModel::where('id', $uid)->delete();
        return $response->withRedirect($this->container->router->pathFor('adminUser'), 200);
    }
}