<?php
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: 黎小龙 <shalinglom@gmail.com>
// +----------------------------------------------------------------------
// | CreateTime: 2018/11/13 
// +----------------------------------------------------------------------


namespace app\common\model;

/**
 * 商家商品子表
 * Class ShopCommodityItem
 * @package app\common\model
 */
class ShopCommodityItem extends Model
{
    public function getStyleAttr($value)
    {
        if ($value) {
            return json_decode($value);
        }
        return '';
    }
}