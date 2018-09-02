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

class Article extends Validate
{
    public $rule = [
        'title' => 'require'
    ];

    public $message = [
        'title.require' => '标题不能为空！'
    ];

    public $scene = [

    ];
}