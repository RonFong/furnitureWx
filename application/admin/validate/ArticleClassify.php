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

class ArticleClassify extends Validate
{
    public $rule = [
        'classify_name' => 'require'
    ];

    public $message = [
        'classify_name.require' => '名称不能为空！'
    ];

    public $scene = [

    ];
}