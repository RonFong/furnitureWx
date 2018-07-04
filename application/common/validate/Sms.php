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
 * sms 短信服务
 * Class Sms
 * @package app\common\validate
 */
class Sms extends BaseValidate
{
    protected $rule = [
        'phoneNumber'  => 'require|isPhoneNo',
        'authCode'     => 'require',
    ];

    protected $message = [
        'phoneNumber.require'      => '手机号不能为空',
        'phoneNumber.isPhoneNo'    => '手机号格式错误',
        'authCode'                 => '验证码不能为空',
    ];

    protected $scene = [
        'getAuthCode'         => [
            'phoneNumber'
        ],
        'checkAuthCode'       => [
            'phoneNumber',
            'authCode'
        ],
    ];
}