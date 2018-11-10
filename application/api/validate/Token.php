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
        'code'      => 'require',
        'userInfo'  => 'require|location',
    ];

    protected $message = [
        'code.require'      => 'code can not empty',
        'userInfo.require'  => 'userInfo can not empty',
    ];

    protected $scene = [
        'getToken'  => [
            'code',
            'userInfo',
        ]
    ];


    protected function location($value)
    {
        if (empty($value['lat'])) {
            return 'lat can not empty';
        }
        if (empty($value['lng'])) {
            return 'lng can not empty';
        }
        return true;
    }
}