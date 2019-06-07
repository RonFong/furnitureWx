<?php
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: 黎小龙 <shalinglom@gmail.com>
// +----------------------------------------------------------------------
// | CreateTime: 2019/6/6 
// +----------------------------------------------------------------------


namespace app\admin\validate;


use think\Validate;

class GoodsAttr extends Validate
{
    /*字段规则*/
    protected $rule = [
        'attr_name' => 'require|unique:goods_attr',
    ];

    /*返回错误信息*/
    protected $message = [
        "attr_name.unique" => '此属性类别名已存在，不能重复！',
    ];

    protected $scene = [

    ];
}