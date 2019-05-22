<?php
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: 黎小龙 <shalinglom@gmail.com>
// +----------------------------------------------------------------------
// | CreateTime: 2019/5/21 
// +----------------------------------------------------------------------


namespace app\admin\validate;


use think\Validate;

class GoodsClassify extends Validate
{
    public $rule = [
        'classify_name'     => 'require|unique:goods_classify',
    ];

    public $message = [
        'classify_name.unique'  => '此分类名已存在'
    ];

    public $scene = [

    ];
}