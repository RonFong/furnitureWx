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

class HomeContent extends Model
{
    /**
     * 获取类型
     * @param string $value
     * @param string $data
     * @return mixed
     */
    public function getGroupTypeNameAttr($value, $data)
    {
        $value = isset($data['group_type']) ? $data['group_type'] : $value;
        $item = ['1'=>'厂家', '2'=>'商家'];
        return isset($item[$value]) ? $item[$value] : '';
    }

    /**
     * 获取类型
     * @param string $value
     * @param string $data
     * @return mixed
     */
    public function getGroupNameAttr($value, $data)
    {
        if ($data['group_type'] == 1) {
            return Db::table('factory')->where('id', $data['group_id'])->value('factory_name');
        }

        if ($data['group_type'] == 2) {
            return Db::table('shop')->where('id', $data['group_id'])->value('shop_name');
        }
        return '查询错误';
    }
}