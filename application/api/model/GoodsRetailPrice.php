<?php
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: 黎小龙 <shalinglom@gmail.com>
// +----------------------------------------------------------------------
// | CreateTime: 2018/8/16 
// +----------------------------------------------------------------------


namespace app\api\model;

use app\common\model\GoodsRetailPrice as CoreGoodsRetailPrice;

/**
 * 商家自定义零售价
 * Class GoodsRetailPrice
 * @package app\api\model
 */
class GoodsRetailPrice extends CoreGoodsRetailPrice
{
    /**
     * 给所有商城商品配置 零售价比例
     * @param $data
     * @return false|int
     * @throws \think\exception\DbException
     */
    public function setGlobalRatio($data)
    {
        $data['shop_id'] = user_info('group_id');
        $data['goods_id'] = 0;
        return $this->save($data);
    }

    /**
     * 给商城商品设置零售价
     * @param $data
     * @return false|int
     * @throws \think\exception\DbException
     */
    public function setGoodsAmount($data)
    {
        $data['shop_id'] = user_info('group_id');
        return $this->save($data);
    }
}
