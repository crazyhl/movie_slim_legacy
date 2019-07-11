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
            new \Twig\TwigFunction('generateLinkItem', [$this, 'generateLinkItem'], ['needs_environment' => true]),
        ];
    }

    public function links(Environment $env, $data, $eachSideCount = 2, $template = 'paginate/links.html')
    {
        $env->load($template)->display(compact('data', 'eachSideCount'));
    }

    public function generateLinkItem(Environment $env, $currentPage, $totalPage, $path, $pageName = 'page', $eachSideCount = 2, $template = 'paginate/link_item.html')
    {
        $totalCount = $eachSideCount * 2 + 1;
        $start = $currentPage - $eachSideCount;
        $end = $currentPage + $eachSideCount;
        if ($totalPage <= $totalCount) {
            $start = 1;
            $end = $totalPage;
        } else {
            if ($currentPage <= $eachSideCount) {
                $start = $currentPage - abs($eachSideCount - $currentPage - 1);
                $end = $currentPage + $eachSideCount + ($eachSideCount - $currentPage + 1);
            }

            if ($currentPage + $eachSideCount > $totalPage) {
                $start = $currentPage - $eachSideCount - ($currentPage + $eachSideCount - $totalPage);
                $end = $currentPage + $eachSideCount - ($currentPage + $eachSideCount - $totalPage);
            }
        }
        $links = [];
        for ($i = $start; $i <= $end; $i++) {
            $links[] = [
                'name' => $i,
                'url' => $path . '&' . $pageName . '=' . $i,
                'isDisabled' => false,
                'isActive' => ($i == $currentPage) ? true : false,
            ];
        }
        $env->load($template)->display(compact('links'));

    }
}