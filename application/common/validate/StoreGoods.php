<?php
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: 黎小龙 <shalinglom@gmail.com>
// +----------------------------------------------------------------------
// | CreateTime: 2018/8/12 
// +----------------------------------------------------------------------


namespace app\common\validate;

/**
 * 商城商品
 * Class StoreGoods
 * @package app\common\validate
 */
class StoreGoods extends BaseValidate
{
    protected $rule = [
        'shop_id'   => 'require'
    ];

    protected $message = [
        'shop_id.require'   => 'shop_id 不能为空'
    ];

    protected $scene = [
        'getGoodsList'  => [
            'shop_id'
        ],
    ];
}