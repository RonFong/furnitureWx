<?php
// +----------------------------------------------------------------------
// | Copyright (c) 2018-{2018} http://www.donglixia.net All rights reserved.
// +----------------------------------------------------------------------
// | Author: 十万马 <962863675@qq.com>
// +----------------------------------------------------------------------
// | DateTime: 2018/9/1 17:13
// +----------------------------------------------------------------------

namespace app\admin\validate;

use think\Validate;

class MusicCategory extends Validate
{
    public $rule = [
        'category_name' => 'require'
    ];

    public $message = [
        'category_name.require' => '分类名称不能为空！'
    ];

    public $scene = [

    ];
}