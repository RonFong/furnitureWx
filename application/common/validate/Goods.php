<?php
// +----------------------------------------------------------------------
// | 深圳市保联科技有限公司
// +----------------------------------------------------------------------
// | Copyright (c) 2013-2017 http://www.luckyins.com All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: 黎小龙 <shalinglom@gmail.com>
// +----------------------------------------------------------------------

namespace app\common\validate;

/**
 * 商城商品
 * Class Goods
 * @package app\common\validate
 */
class Goods extends BaseValidate
{
    protected $rule = [
        'shop_id'       => 'require|number',
        'product_id'    => 'require|number',
        'page'          => 'require|number',
        'row'           => 'require|number',
        'configure_id'  => 'require|number',
        'retail_price'  => 'require|number',
    ];

    protected $message = [
        'retail_price.require'  => '请填写自定义零售价',
        'retail_price.number'   => '零售价必须为整数',
    ];

    protected $scene = [
        'getList'   => [
            'shop_id',
            'page',
            'row'
        ],
        'info'  => [
            'shop_id',
            'product_id'
        ],
        'setRetailPrice'    => [
            'shop_id'   => 'require|number|isCurrentShop',
            'product_id',
            'configure_id',
            'retail_price',
        ],
    ];

    /**
     * 判断当前用户是否为 商城入口商家
     * @param $value
     * @param $rule
     * @param $data
     * @return bool|string
     */
    protected function isCurrentShop($value, $rule, $data)
    {
        if (user_info('type') != 2 || user_info('group_id') != $value) {
            return '非当前商家';
        }
        return true;
    }
}