<?php
// +----------------------------------------------------------------------
// | Copyright (c) 2018-{2018} http://www.donglixia.net All rights reserved.
// +----------------------------------------------------------------------
// | Author: 十万马 <962863675@qq.com>
// +----------------------------------------------------------------------
// | DateTime: 2018/8/4 19:59
// +----------------------------------------------------------------------
namespace app\admin\validate;

use think\Validate;

class ShopClassify extends Validate
{
    /*字段规则*/
    protected $rule = [
        'classify_name' => 'require|unique:shop_classify'
    ];

    /*返回错误信息*/
    protected $message = [
        "classify_name.require" => '经营类别名称不能为空！',
        "classify_name.unique" => '此经营类别已存在！',
    ];

    protected $scene = [
        'save' => ['classify_name']
    ];
}