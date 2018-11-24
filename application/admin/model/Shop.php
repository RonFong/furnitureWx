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
use think\Db;

class Shop extends CoreShop
{
    /**
     * 获取管理员名称
     * @param $value
     * @param $data
     * @return mixed|string
     */
    public function getAdminUserNameAttr($value, $data)
    {
        $id = $data['admin_user'] ?? $value;
        $user = User::get($id);
        return $user->user_name ?? '';
    }

    /**
     * 获取状态中文名
     * @param $value
     * @param $data
     * @return string
     */
    public function getStateDesAttr($value, $data)
    {
        $value = $data['state'] ?? $value;
        return $value ? '启用' : '禁用';
    }

    /**
     * 获取审核状态名称
     * @param $value
     * @param $data
     * @return mixed
     */
    public function getAuditStateDesAttr($value, $data)
    {
        $value = $data['audit_state'] ?? $value;
        $item = ['未审核', '通过', '不通过'];
        return $item[$value];
    }

    /**
     * 获取是否有首页图文
     * @param $value
     * @param $data
     * @return mixed
     */
    public function getHomeContentHasAttr($value, $data)
    {
        $value = $data['id'] ?? $value;
        $count = Db::name('home_content')->where('group_type', 2)->where('group_id', $value)->count();
        return $count > 0 ? '有' : '无';
    }

    /**
     * 获取商品数量
     * @param $value
     * @param $data
     * @return mixed
     */
    public function getShopCommodityCountAttr($value, $data)
    {
        $value = $data['id'] ?? $value;
        return Db::name('shop_commodity')->where('shop_id', $value)->where('state', 1)->count();
    }

}