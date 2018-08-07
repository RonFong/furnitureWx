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
     * 获取人员列表
     * @param $map
     * @return false|\PDOStatement|string|\think\Collection
     */
    public function getUserList($map = [])
    {
        if (empty($map)) {
            $map[] = ['exp', '1=1'];
        }
        return Db::name('user')->where($map)->field('id,account')->select();
    }

}