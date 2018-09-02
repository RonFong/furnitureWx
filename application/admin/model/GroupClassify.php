<?php
// +----------------------------------------------------------------------
// | Copyright (c) 2018-2018 http://www.donglixia.net All rights reserved.
// +----------------------------------------------------------------------
// | Author: 十万马 <962863675@qq.com>
// +----------------------------------------------------------------------
// | DateTime: 2018-02-09 16:17
// +----------------------------------------------------------------------

namespace app\admin\model;

use app\common\model\Model;
use think\Db;

class GroupClassify extends Model
{
    /**
     * 获取上级分类名称
     * @param string $value
     * @param string $data
     * @return mixed
     */
    public function getParentTextAttr($value, $data)
    {
        $value = isset($data['parent_id']) ? $data['parent_id'] : $value;
        return $this->where('id', $value)->value('classify_name');
    }

    /**
     * 获取类型
     * @param string $value
     * @param string $data
     * @return mixed
     */
    public function getGroupTypeNameAttr($value, $data)
    {
        $value = isset($data['parent_id']) ? $data['parent_id'] : $value;
        $item = ['1'=>'厂家', '2'=>'商家'];
        return isset($item[$value]) ? $item[$value] : '';
    }

    /**
     * 获取厂/商家名称
     * @param string $value
     * @param string $data
     * @return mixed
     */
    public function getGroupNameAttr($value, $data)
    {
        $value = isset($data['group_id']) ? $data['group_id'] : $value;
        $res = '';
        if (isset($data['group_type'])) {
            if ($data['group_type'] == 1) {
                $res = Db::name('factory')->where('id', $value)->value('factory_name');
            } elseif ($data['group_type'] == 2) {
                $res = Db::name('shop')->where('id', $value)->value('shop_name');
            }
        }
        return $res;
    }

}