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

class Product extends Validate
{
    public $rule = [
        'status' => 'number|hasRemark',      //审核结果
        'goods_classify_id' => 'number|isReview',  // 商城分类
    ];

    public $message = [

    ];

    public $scene = [

    ];

    /**
     * 是否填写 审核说明
     * @param $value
     * @param $data
     * @return bool|string
     */
    protected function hasRemark($value, $rule, $data)
    {

        if ($value == 2 && $data['remark'] == '') {
            return '审核不通过时，必须填写说明';
        }
        return true;
    }

    /**
     * @param $value
     * @param $data
     * @return bool|string
     */
    protected function isReview($value, $rule, $data)
    {
        if (empty($value) && $data['status'] == 1) {
            return '审核通过状态，必须为产品选择商城分类';
        }
        return true;
    }
}