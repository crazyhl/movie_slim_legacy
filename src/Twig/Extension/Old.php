<?php


namespace App\Twig\Extension;

use App\Utils\Val;
use Slim\Flash\Messages;
use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;
use Twig\TwigFunction;

/**
 * twig 模板分页扩展
 * Class Paginate
 * @package App\Twig\Extension
 */
class Old extends AbstractExtension
{
    public function getFunctions()
    {
        return [
            new TwigFunction('old', [$this, 'old']),
            new TwigFunction('error', [$this, 'error']),
        ];
    }

    public function getFilters()
    {
        return [
            new TwigFilter('old', [$this, 'old']),
            new TwigFilter('error', [$this, 'error']),
        ];
    }

    public function old($name = '', $keyName = 'old')
    {
        /**
         * @var $flash Messages
         */
        $flash = Val::getInstance()['container']->flash;

        $message = $flash->getFirstMessage($keyName) ?: [];
        if (empty($name)) {
            return $message;
        } else {
            return $message[$name] ?: '';
        }
    }

    public function error()
    {
        return $this->old(null, 'error');
    }
}