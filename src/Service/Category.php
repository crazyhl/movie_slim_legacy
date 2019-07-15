<?php


namespace App\Service;


class Category
{
    /**
     * 把分类分组
     * @param $categories
     * @return array
     */
    public static function groupCategory($categories)
    {
        $groupList = [];
        // 反序数组，这个操作是为了能够在 array_unshift 的时候把数据都放置进去，
        // 因为如果不反序，在没有索引的情况下会导致数据不全，加上索引又不安全，所以，采用反序的方式
        $categories = array_reverse($categories);
        $categoryIdArr = array_column($categories, 'id');
        $groupIdArr = [];
        foreach ($categories as $index => $category) {
            if ($category['parent_id']) {
                $parentIndex = array_search($category['parent_id'], $categoryIdArr);
                if ($parentIndex !== false) {
                    if (empty($categories[$parentIndex]['children'])) {
                        $categories[$parentIndex]['children'] = [];
                    }
                    // 因为前面反序了，所以这边也要反着来，后进来的数据插入到数组头部，就保证顺序争取了
                    array_unshift($categories[$parentIndex]['children'], $categories[$index]);
                }
            } else {
                $groupIdArr[] = $index;
            }
        }
        foreach ($groupIdArr as $index) {
            $groupList[] = $categories[$index];
        }

        $groupList = array_reverse($groupList);

        return $groupList;
    }

    public static function groupToTree($categories, $nameFieldName = 'name', $depth = 0, $prefix = '', $pad = '-')
    {
        // 把分组的数据变成树形
        static $treeList = [];
        foreach ($categories as $category) {
            $childrenList = $category['children'];
            unset($category['children']);
            $category[$nameFieldName] = str_pad($category[$nameFieldName], strlen($category[$nameFieldName]) + $depth * 3 , $pad, STR_PAD_LEFT);
            $treeList[] = $category;
            if ($childrenList) {
                $newDepth = $depth + 1;
                self::groupToTree($childrenList, $nameFieldName, $newDepth);
            }
        }
        return $treeList;
    }
}