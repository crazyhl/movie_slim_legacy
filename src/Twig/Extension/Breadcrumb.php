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
class Breadcrumb extends AbstractExtension
{
    public function getFunctions()
    {
        return [
            new TwigFunction('breadcrumb', [$this, 'breadcrumb'], ['needs_environment' => true]),
        ];
    }

    public function breadcrumb(Environment $env, $data, $extraNavClass='', $extraOlClass='', $template = 'breadcrumb/index.html')
    {
        $env->load($template)->display(compact('data', 'extraNavClass', 'extraOlClass'));
    }
}