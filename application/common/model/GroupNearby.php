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


use traits\model\SoftDelete;


class GroupNearby extends Model
{
    use SoftDelete;

    public function store($id, $type)
    {
        if ($type == 1) {
            $model = new Factory();
            $info = $model
                ->where('id', $id)
                ->field('id as group_id, factory_name as group_name, lng, lat, state, create_time, delete_time')
                ->find()
                ->toArray();
        }
        if ($type == 2) {
            $model = new Shop();
            $info = $model
                ->where('id', $id)
                ->field('id as group_id, shop_name as group_name, lng, lat, state, create_time, delete_time')
                ->find()
                ->toArray();
        }
        $info['group_type'] = $type;
        $id = $this->where(['group_id' => $id, 'group_type' => $type])->value('id');
        if ($id) {
            $info['id'] = $id;
        }
        $this->save($info);
    }
}