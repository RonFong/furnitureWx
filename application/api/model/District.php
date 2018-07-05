<?php

namespace app\api\model;

use app\common\model\District as CoreDistrict;


class District extends CoreDistrict
{
    public function getAllRegion()
    {
//        $province = $this->field(['id','name'])->where('parent_id',0)->select();
//        $city = $this->field(['id','name','parent_id'])->where('level',2)->select();
//        $district = $this->field(['id','name','parent_id'])->where('level',3)->select();
//        var_dump($district);die;
        $items = $this->field(['id','parent_id','name','level'])->select();
//        $res = [];
//        $province = [];
//        $city = [];
//        $district = [];
//        foreach ($data as $key => $list){
////            var_dump($list);die;
//            if($list['parent_id'] == 0){
//                $province[$list['id']] = [
//                    'id' => $list['id'],
//                    'name' => $list['name'],
//                    'city' => [],
//                ];
//            }
//        }
//        var_dump($items);die;

        $data = [];
        foreach ($items as &$item){
            $data[] = $item->toArray();
        }

        $tree = [];
        foreach ($data as $value){
            if (isset($data[$value['parent_id']])){
                $items[$value['parent_id']]['list'] = [];
                $items[$value['parent_id']]['list'][] = &$data[$value['id']];
            }else{
                $tree[] = &$data[$value['id']];
            }

        }
        print_r($tree);
    }
}