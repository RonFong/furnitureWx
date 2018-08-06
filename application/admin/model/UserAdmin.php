<?php
// +----------------------------------------------------------------------
// | Copyright (c) 2018-2018 http://www.donglixia.net All rights reserved.
// +----------------------------------------------------------------------
// | Author: 十万马 <962863675@qq.com>
// +----------------------------------------------------------------------
// | DateTime: 2018-02-09 16:17
// +----------------------------------------------------------------------

namespace app\admin\model;

use app\common\model\UserAdmin as CoreUserAdmin;
use think\Db;

class UserAdmin extends CoreUserAdmin
{

    /**
     * 获取用户类型
     * @param $value
     * @param $data
     * @return mixed
     */
    public function getTypeTextAttr($value, $data)
    {
        $value = isset($data['type']) ? $data['type'] : 0;
        return $value == 1 ? '超级管理员' : '管理员';
    }

    /**
     * 获取角色名称
     * @param $value
     * @param $data
     * @return mixed
     */
    public function getRoleIdTextAttr($value, $data)
    {
        $value = isset($data['role_id']) ? $data['role_id'] : 0;
        return !empty($value) ? Db::table('role')->where('id', $value)->value('role_name') : '';
    }

    /**
     * 获取角色列表
     */
    public function getRoleList()
    {
        return Db::table('role')->select();
    }

}