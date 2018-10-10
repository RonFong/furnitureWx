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

namespace app\common\model;


class StoreClassify extends Model
{
    /**
     * @param $value
     * @param $data
     * @return bool|string
     * @throws \think\exception\DbException
     */
    public function getParentNameAttr($value, $data)
    {
        if ($data['parent_id'] != 0) {
            $classify = self::get($data['parent_id']);
            return $classify->getData('name');
        }
        return '';
    }

    public function getStateTextAttr($value, $data)
    {
        return $data['state'] != 0 ? '启用' : '禁用';
    }
}