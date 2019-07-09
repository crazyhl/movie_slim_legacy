<?php


namespace App\Twig\Extension;

use Twig\Environment;
use Twig\Extension\AbstractExtension;

/**
 * twig 模板分页扩展
 * Class Paginate
 * @package App\Twig\Extension
 */
class Paginate extends AbstractExtension
{
    public function getFunctions()
    {
        return [
            new \Twig\TwigFunction('links', [$this, 'links'], ['needs_environment' => true]),
        ];
    }

    public function links(Environment $env,  $data, $template = 'paginate/links.html')
    {
        $number = $data->totalPage;
        return $env->load($template)->render(compact('number'));
    }
}