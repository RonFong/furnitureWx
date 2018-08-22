<?php
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: 黎小龙 <shalinglom@gmail.com>
// +----------------------------------------------------------------------
// | CreateTime: 2018/8/22 
// +----------------------------------------------------------------------


namespace app\common\validate;


class Music extends BaseValidate
{
    protected $rule = [
        'category_id'       => 'require|number',
        'query'             => 'require',
    ];

    protected $message = [
        'category_id.require'   => '类别id不能为空',
        'query.require'         => '查询条件不能空',
    ];

    protected $scene = [
        'getByCategory'    => [
            'category_id',
        ],
        'query'      => [
            'query',
        ],
    ];
}