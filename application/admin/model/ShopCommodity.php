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

class ShopCommodity extends Model
{
    /**
     * 获取商户名
     * @param $value
     * @param $data
     * @return mixed
     */
    public function getShopNameAttr($value, $data)
    {
        $value = isset($data['shop_id']) ? $data['shop_id'] : $value;
        return Db::name('shop')->where('id', $value)->value('shop_name');
    }

}