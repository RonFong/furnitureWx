<?php
// +----------------------------------------------------------------------
// | 深圳市保联科技有限公司
// +----------------------------------------------------------------------
// | Copyright (c) 2013-2017 http://www.luckyins.com All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: 黎小龙 <shalinglom@gmail.com>
// +----------------------------------------------------------------------

namespace app\admin\model;

use app\common\model\District as CoreDistrict;

class District extends CoreDistrict
{
    /**
     * 省市区三级联动，根据parent_id获取省市区下拉框
     * @param $id int 对应表 district 中的 parent_id
     * @return false|\PDOStatement|string|\think\Collection
     */
    public function getArea($id)
    {
        return self::all(function($query) use ($id) {
            $query->where('parent_id', $id)->field('id, parent_id, code, name');
        });
    }

}