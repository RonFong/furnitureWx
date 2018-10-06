<?php
// +----------------------------------------------------------------------
// | Copyright (c) 2018-2018 http://www.donglixia.net All rights reserved.
// +----------------------------------------------------------------------
// | Author: 十万马 <962863675@qq.com>
// +----------------------------------------------------------------------
// | DateTime: 2018-02-09 16:17
// +----------------------------------------------------------------------

namespace app\admin\model;

use app\common\model\Factory as CoreFactory;
use think\Db;

class Factory extends CoreFactory
{
    public function getAdminUserNameAttr($value, $data)
    {
        return Db::table('user')->where('id', $data['admin_user'])->value('user_name');
    }

    public function getAuditStateTextAttr($value, $data)
    {
        $array = ['未审核', '通过', '未通过'];
        return $array[$data['audit_state']];
    }
}