<?php


namespace App\Twig\Extension;

use Twig\Environment;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

/**
 * twig 模板分页扩展
 * Class Paginate
 * @package App\Twig\Extension
 */
class DefaultExtra extends AbstractExtension
{
    public function getFunctions()
    {
        return [
            new TwigFunction('defaultExtra', [$this, 'defaultExtra']),
        ];
    }

    public function defaultExtra($value, $default = '')
    {
        if (is_numeric($value)) {
            return $value;
        }

        return $value ? $value : $default;
    }

}