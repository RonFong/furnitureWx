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
//        $tree = array();
//        foreach ($all as $key => $item){
//            if (isset($all[$item['parent_id']]))
//                $all[$item['parent_id']]['son'][] = &$all[$item['id']];
//            else
//                $tree[] = &$all[$item['id']];
//        }
        $tree = $this->getTree($all,0);
        dump($tree);die;
    }

    public function getTree($data, $pId)
    {
        $tree = '';
        foreach($data as $k => $v)
        {
            if($v['parent_id'] == $pId)
            {        //父亲找到儿子
                $v['parent_id'] = $this->getTree($data, $v['parent_id']);
                $tree[] = $v;
            }
        }
        return $tree;
    }
}