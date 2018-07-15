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


class Article extends BaseValidate
{
    protected $rule = [
        'id'                => 'require|number|egt:1',
        'title'             => 'require',
        'classify_id'       => 'require|number',
        'content'           => 'require|checkImageText',
    ];

    protected $message = [
        'title.require'         => '标题不能为空',
        'classify_id.require'   => '分类不能为空',
        'content.require'       => '图文内容不能为空',
    ];

    protected $scene = [
        'create'        => [
            'id'        => 'idCantExist',
            'title',
            'classify_id',
            'content',
        ],
        'update'        => [
            'id',
            'title',
            'classify_id',
            'content',
        ],
        'delete'        => [
            'id'
        ],
    ];

    protected function idCantExist($value, $rule, $data)
    {
        if (array_key_exists('id', $data))
            return '新增操作，不能带有主键参数';
        if (array_key_exists('content', $data) && is_array($data['content'])) {
            foreach ($data['content'] as $v) {
                if (array_key_exists('id', $v))
                    return '新增操作，不能带有主键参数';
            }
        }
        return true;
    }

}
