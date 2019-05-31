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

    /**
     * @param $value
     * @param $data
     * @return int
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function getHomeViewAttr($value, $data)
    {
        return (new HomeContent())->where(['group_type' => 1, 'group_id' => $data['id']])->find() ? 1 : 0;
    }
}