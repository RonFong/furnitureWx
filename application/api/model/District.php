<?php

namespace app\api\model;

use app\common\model\District as CoreDistrict;


class District extends CoreDistrict
{
    public function getRegionData($parent_id = '',$level = '')
    {
        $data = $this->field(['id','name'])->where('parent_id',$parent_id)->where('level',$level)->select();
        return $data;
    }
}