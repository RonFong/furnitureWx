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

namespace app\admin\validate;


class Factory
{
    protected $rule = [
        'factory_name' => 'require|token',
        'user_id'      => 'require',
    ];

    protected $message = [
        'factory_name.token'    => '请勿重复提交',
        'factory_name.require'  => '厂家名不能为空',
        'user_id'               => '请分配初始用户'
    ];

    protected $scene = [
        'edit' => ['factory_id', 'user_id'],
    ];
}