<?php

namespace app\api\model;

use app\common\model\District as CoreDistrict;


class District extends CoreDistrict
{
    public function getRegionData($parent_id = '',$level = '')
    {
        $data = $this->field(['id','name'])->where('parent_id',$parent_id)->where('level',$level)->select();
//        $data = collection($data)->toArray();
//        $data   = array_column($data,'name','id');  //键值
//        $result['ids'] = array_keys($data);
//        $result['region'] = $data;
        return $data;
//        var_dump($result);die;
    }
}