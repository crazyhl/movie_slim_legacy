<?php


namespace App\Utils;


use Slim\Http\Request;

class Old
{
    public static function get()
    {
        $container = Val::getInstance()['container'];
        /**
         * @var $request Request
         */
        $request = $container->request;
        $old = $request->getQueryParams();
        if ($request->getMethod() == 'POST') {
            $old += ($request->getParsedBody() ?: []);
        }

        return $old;
    }
}