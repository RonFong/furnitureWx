<?php

namespace app\api\model;

use app\common\model\Category as CoreCategory;


class Category extends CoreCategory
{
    protected $table='business_category';

    public function getAllCategory()
    {
        $fields = ['id','parent_id','name'];
        $all = $this->field($fields)->select();

        $all = collection($all)->toArray();
        return array_values($this->formatTree($all,0));
    }

    public function formatTree($arr,$pid=0){
        foreach($arr as $k => $v){
            if($v['parent_id']==$pid){
                $data[$v['id']]=$v;
                $data[$v['id']]['son']=$this->formatTree($arr,$v['id']);
            }
        }
        return isset($data)?$data:array();
    }

    /**
     * 排序
     * @param $arr
     * @param $cols
     * @return mixed
     */
//    public function sort($arr,$cols){
//        //子分类排序
//        foreach ($arr as $k => &$v) {
//            if(!empty($v['sub'])){
//                $v['sub']=$this->sort($v['sub'],$cols);
//            }
//            $sort[$k]=$v[$cols];
//        }
//        if(isset($sort))
//            array_multisort($sort,SORT_DESC,$arr);
//        return $arr;
//    }
}