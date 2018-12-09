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
        'classify_id'       => 'require|number',
        'content'           => 'require',
        'page'              => 'checkPageAndRow',
        'user_id'           => 'require|number|egt:1',
        'article_id'        => 'require|number|egt:1',
        'title'             => 'require',
        'is_draft'          => 'in:0,1',
        'order_by'          => 'in:read_num,create_time,distance'     //人气、时间、距离
    ];

    protected $message = [
        'content.require'       => '图文内容不能为空',
        'title.require'         => '请输入标题',
    ];

    protected $scene = [
        'create'        => [
            'id'        => 'idCantExist',
            'title',
            'classify_id',
            'content',
            'title',
            'is_draft'
        ],
        'update'        => [
            'id',
            'classify_id',
            'content',
            'title',
            'is_draft'
        ],
        'delete'        => [
            'id'
        ],
        'list' => [
            'classify_id' => 'number',
            'page',
            'order_by',
            'is_draft'
        ],
        'details'       => [
            'id'
        ],
        'share'       => [
            'id'
        ],
        'getByUserId'   => [
            'user_id'
        ],
        'listByClassify'    => [
            'classify_id',
        ],
        'getArticleContent'       => [
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
