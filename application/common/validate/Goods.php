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
        'shop_id'   => 'require|number',
    ];

    protected $message = [

    ];

    protected $scene = [
        'getList'   => [
            'shop_id'
        ],
    ];
}