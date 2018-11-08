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

namespace app\api\validate;


use app\common\validate\BaseValidate;

class Token extends BaseValidate
{
    protected $rule = [
        'code'      => 'require|string',
        'userInfo'  => 'require',
        'lat'       => 'require',
        'lng'       => 'require'
    ];

    protected $message = [
        'code.require'      => 'code can not empty',
        'userInfo.require'  => 'userInfo can not empty',
        'lat.require'       => 'lat can not empty',
        'lng.require'       => 'lng can not empty',
    ];

    protected $scene = [
        'getToken'  => [
            'code',
            'userInfo',
            'lat',
            'lng'
        ]
    ];
}