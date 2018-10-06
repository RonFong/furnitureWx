<?php
// +----------------------------------------------------------------------
// | Copyright (c) 2018-2018 http://www.donglixia.net All rights reserved.
// +----------------------------------------------------------------------
// | Author: 十万马 <962863675@qq.com>
// +----------------------------------------------------------------------
// | DateTime: 2018-02-09 16:17
// +----------------------------------------------------------------------

namespace app\admin\model;

use app\common\model\Shop as CoreShop;

class Shop extends CoreShop
{
    public function getAdminUserNameAttr($value, $data)
    {
        $user = User::get($value);
        return $user->user_name;
    }

    public function getStateDesAttr($value)
    {
        return $value ? '启用' : '禁用';
    }

    public function getAuditStateDesAttr($value)
    {
        $array = ['未审核', '通过', '不通过'];
        return $array[$value];
    }
}