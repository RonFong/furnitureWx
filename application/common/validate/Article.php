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
        'content'           => 'require|checkImageText',
        'page'              => 'checkPageAndRow',
        'user_id'           => 'require|number|egt:1',
        'article_id'        => 'require|number|egt:1',
        'order'             => 'require',
        'title'             => 'require|max:10',
    ];

    protected $message = [
        'content.require'       => '图文内容不能为空',
        'title.max'             => '标题过长',
    ];

    protected $scene = [
        'create'        => [
            'id'        => 'idCantExist',
            'title',
            'classify_id',
            'content',
            'title'
        ],
        'update'        => [
            'id',
            'classify_id',
            'content',
            'title'
        ],
        'delete'        => [
            'id'
        ],
        'localArticleList' => [
            'page',
            'order'
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
        'moreComment'   => [
            'article_id',
            'page'
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
