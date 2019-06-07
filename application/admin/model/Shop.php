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
     * 经营类别
     * @param $value
     * @param $data
     * @return mixed|string
     */
    public function getClassifyNameAttr($value, $data)
    {
        if ($data['classify_id'] == 0) {
             return '/';
        }
        return Db::table('goods_classify')->where('id', $data['classify_id'])->value('classify_name');
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
     * 商店创始人最后一次登录时间
     * @param $value
     * @param $data
     * @return mixed
     */
    public function getLastLoginTimeAttr($value, $data)
    {
        $value = $data['admin_user'] ?? $value;
        $res = !empty($value) ? Db::name('user_location')->where('user_id', $value)->order('id desc')->value('create_time') : '';
        return !empty($res) ? date('Y-m-d H:i:s', $res) : '';
    }

    /**
     * 商店员工总登录次数
     * @param $value
     * @param $data
     * @return mixed
     */
    public function getAllLoginTimesAttr($value, $data)
    {
        $value = $data['id'] ?? $value;
        $user_ids = Db::name('user')->where('type', 2)->where('group_id', $value)->column('id');
        return Db::name('user_location')->whereIn('user_id', $user_ids)->count();
    }

    /**
     * 商店员工本月登录次数
     * @param $value
     * @param $data
     * @return mixed
     */
    public function getAllLoginTimesMonthAttr($value, $data)
    {
        $value = $data['id'] ?? $value;
        $user_ids = Db::name('user')->where('type', 2)->where('group_id', $value)->column('id');
        return Db::name('user_location')->whereIn('user_id', $user_ids)->where('create_time', '>', mktime(0,0,0,date('m'),1,date('Y')))->count();
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