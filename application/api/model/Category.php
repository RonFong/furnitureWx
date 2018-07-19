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
            if (isset($all[$item['parent_id']]))
                $all[$item['parent_id']]['son'][] = &$all[$item['id']];
            else
                $tree[] = &$all[$item['id']];
            return $tree;
        }
        dump($tree);die;
    }
}