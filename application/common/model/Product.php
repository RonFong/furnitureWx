<?php
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: 黎小龙 <shalinglom@gmail.com>
// +----------------------------------------------------------------------
// | CreateTime: 2019/2/27 
// +----------------------------------------------------------------------


namespace app\common\model;


use think\Db;
use think\Request;
use traits\model\SoftDelete;

class Product extends Model
{
    use SoftDelete;

    /**
     * 获取产品多个价格中的最低价
     * @param $value
     * @return mixed
     */
    protected function getRetailPriceAttr($value)
    {
        $shopId = Request::instance()->param('shop_id');
        $rate = $this->getProductPriceRate($value, $shopId);
        $retailPrice = Db::table('product_price')->where('product_id', $value)->min('trade_price') * $rate;
        return format_price($retailPrice);
    }

    /**
     * 获取颜色图
     * @param $value
     * @return mixed
     */
    protected function getImgAttr($value)
    {
        $colorId = Db::table('product_price')->where('product_id', $value)->order('trade_price')->value('color_id');
        return Db::table('product_color')
            ->where('id', $colorId)
            ->value('img');
    }

    /**
     * 获取产品零售价倍率
     * @param $productId
     * @param $shopId
     * @return mixed
     */
    public function getProductPriceRate($productId, $shopId)
    {
        $rate = Db::table('product_retail_price')->where([
            'shop_id'   => $shopId,
            'product_id'    => $productId
        ])->value('rate');
        if (!$rate) {
            $rate = Db::table('product_retail_rate')->where('shop_id', $shopId)->value('rate');
        }
        return $rate ?? config('system.price_ratio');
    }

}