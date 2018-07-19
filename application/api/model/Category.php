<?php

namespace app\api\model;

use app\common\model\Category as CoreCategory;


class Category extends CoreCategory
{
    protected $table='shop_category';

    public function getAllCategory()
    {
        $all = $this->select();
        $all = collection($all)->toArray();
        //$items[$item['pid']]['son'][$item['id']] = &$items[$item['id']];
        //    return isset($items[0]['son']) ? $items[0]['son'] : array();
        $tree = array();
        foreach ($all as $key => $item){
            if (isset($items[$item['parent_id']]))
                $items[$item['parent_id']]['son'][] = &$items[$item['id']];
            else
                $tree[] = &$items[$item['id']];
            return $tree;
        }
        dump($tree);
    }
}