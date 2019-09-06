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
class Paginate extends AbstractExtension
{
    public function getFunctions()
    {
        return [
            new TwigFunction('links', [$this, 'links'], ['needs_environment' => true]),
            new TwigFunction('links2', [$this, 'links2'], ['needs_environment' => true]),
        ];
    }

    public function links(Environment $env, $data, $showTotal = true, $extraClass='', $eachSideCount = 2, $pageName = 'page', $template = 'paginate/links.html')
    {
        $links = $this->generateLinkItem($data['currentPage'], $data['totalPage'], $data['path'], $eachSideCount, $pageName);
        $env->load($template)->display(compact('data', 'links', 'showTotal', 'extraClass'));
    }

    public function links2(Environment $env, $data, $showTotal = true, $extraClass='', $eachSideCount = 3, $pageName = 'page', $template = 'paginate/links.html')
    {
        $links = $this->generateLinkItem2($data['currentPage'], $data['totalPage'], $data['path'], $eachSideCount, $pageName);
        $env->load($template)->display(compact('data', 'links', 'showTotal', 'extraClass'));
    }

    private function generateLinkItem($currentPage, $totalPage, $path, $eachSideCount = 2, $pageName = 'page')
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

        if ($totalPage > $totalCount) {
            if ($currentPage > $eachSideCount + 1) {
                $links[] = $this->generateLinkData('首页', $path, 1, $currentPage, false, null, $pageName);
            }

            $links[] = $this->generateLinkData("«", $path, ($currentPage == 1 ? 1 : ($currentPage - 1)), $currentPage, ($currentPage == 1 ? true : false), false, $pageName);
        }


        for ($i = $start; $i <= $end; $i++) {
            $links[] = $this->generateLinkData($i, $path, $i, $currentPage, false, null, $pageName);
        }

        if ($totalPage > $totalCount) {
            $links[] = $this->generateLinkData("»", $path, ($currentPage == $totalPage ? $totalPage : ($currentPage + 1)), $currentPage, ($currentPage == $totalPage ? true : false), false, $pageName);

            if ($totalPage - $currentPage > 2) {
                $links[] = $this->generateLinkData('末页', $path, $totalPage, $currentPage, false, null, $pageName);
            }
        }

        return $links;
    }


    private function generateLinkItem2($currentPage, $totalPage, $path, $eachSideCount = 3, $pageName = 'page')
    {
        $totalCount = $eachSideCount * 2 + 6;
        $start = ($currentPage - $eachSideCount < 1) ? 1 : $currentPage - $eachSideCount;
        $end = ($currentPage + $eachSideCount > $totalPage) ? $totalPage : $currentPage + $eachSideCount;

        $links = [];
        $links[] = $this->generateLinkData("«", $path, ($currentPage == 1 ? 1 : ($currentPage - 1)), $currentPage, ($currentPage == 1 ? true : false), false, $pageName);

        if ($totalPage <= $totalCount) {
            $start = 1;
            $end = $totalPage;
            for ($i = $start; $i <= $end; $i++) {
                $links[] = $this->generateLinkData($i, $path, $i, $currentPage, false, null, $pageName);
            }
        } else {

            if ($currentPage < $eachSideCount * 2 + 2) {
                for ($i = 1; $i <= $eachSideCount * 2 + 2; $i++) {
                    $links[] = $this->generateLinkData($i, $path, $i, $currentPage, false, null, $pageName);
                }
                $links[] = $this->generateLinkData('...', false, '', $currentPage, true, null, $pageName);
                for ($i = $totalPage - 1; $i <= $totalPage; $i++) {
                    $links[] = $this->generateLinkData($i, $path, $i, $currentPage, false, null, $pageName);
                }
            } else if ($currentPage > $totalPage - ($eachSideCount * 2 + 2)) {
                for ($i = 1; $i <= 2; $i++) {
                    $links[] = $this->generateLinkData($i, $path, $i, $currentPage, false, null, $pageName);
                }
                $links[] = $this->generateLinkData('...', false, '', $currentPage, true, null, $pageName);
                for ($i = $totalPage - ($eachSideCount * 2 + 2); $i <= $totalPage; $i++) {
                    $links[] = $this->generateLinkData($i, $path, $i, $currentPage, false, null, $pageName);
                }
            } else {
                for ($i = 1; $i <= 2; $i++) {
                    $links[] = $this->generateLinkData($i, $path, $i, $currentPage, false, null, $pageName);
                }
                $links[] = $this->generateLinkData('...', false, '', $currentPage, true, null, $pageName);
                for ($i = $start; $i <= $end; $i++) {
                    $links[] = $this->generateLinkData($i, $path, $i, $currentPage, false, null, $pageName);
                }
                $links[] = $this->generateLinkData('...', false, '', $currentPage, true, null, $pageName);
                for ($i = $totalPage - 1; $i <= $totalPage; $i++) {
                    $links[] = $this->generateLinkData($i, $path, $i, $currentPage, false, null, $pageName);
                }
            }

        }

//        $links[] = $this->generateLinkData('...', false, '', $currentPage, true, null, $pageName);

        $links[] = $this->generateLinkData("»", $path, ($currentPage == $totalPage ? $totalPage : ($currentPage + 1)), $currentPage, ($currentPage == $totalPage ? true : false), false, $pageName);

        return $links;
    }

    private function generateLinkData($name, $path, $index, $currentPage, $isDisable = false, $isActive = null, $pageName = 'page')
    {
        return [
            'name' => $name,
            'url' => $path . (stripos($path, '?') ? '&' : '?') . $pageName . '=' . $index,
            'isDisabled' => $isDisable,
            'isActive' => $isActive === null ? (($index == $currentPage) ? true : false) : $isActive,
            'isLink' => $path === false ? false : true,
        ];
    }
}