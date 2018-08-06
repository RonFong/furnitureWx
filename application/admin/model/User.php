<?php
// +----------------------------------------------------------------------
// | Copyright (c) 2018-2018 http://www.donglixia.net All rights reserved.
// +----------------------------------------------------------------------
// | Author: 十万马 <962863675@qq.com>
// +----------------------------------------------------------------------
// | DateTime: 2018-02-09 16:17
// +----------------------------------------------------------------------

namespace app\admin\model;

use app\common\model\User as CoreUser;
use think\Db;

class User extends CoreUser
{

    /**
     * 获取用户类型
     * @param $value
     * @param $data
     * @return mixed
     */
    public function getTypeTextAttr($value, $data)
    {
        $item = ['1'=>'厂家用户', '2'=>'商家用户', '3'=>'普通用户'];
        $value = isset($data['type']) ? $data['type'] : 0;
        return isset($item[$value]) ? $item[$value] : '';
    }

    /**
     * 获取用户性别
     * @param $value
     * @param $data
     * @return mixed
     */
    public function getGenderTextAttr($value, $data)
    {
        $item = ['0'=>'未知', '1'=>'男', '2'=>'女'];
        $value = isset($data['gender']) ? $data['gender'] : 0;
        return isset($item[$value]) ? $item[$value] : '';
    }


    /**
     * 获取用户状态
     * @param $value
     * @param $data
     * @return mixed
     */
    public function getStateTextAttr($value, $data)
    {
        $item = ['0'=>'冻结', '1'=>'启用'];
        $value = isset($data['state']) ? $data['state'] : 0;
        return isset($item[$value]) ? $item[$value] : '';
    }

    /**
     * 获取厂/商家列表
     * @param $type
     * @return array|false|\PDOStatement|string|\think\Collection
     */
    public function getGroupList($type = 0)
    {
        $list = [];
        if ($type==1) {
            $list = Db::table('factory')->field('id,factory_name as name')->select();
        } elseif ($type == 2) {
            $list = Db::table('shop')->field('id,shop_name as name')->select();
        }
        return $list;
    }
}