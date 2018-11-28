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

use app\common\model\Article;
use \app\common\model\ArticleComment as ArticleCommentModel;

/**
 * 文章评论
 * Class ArticleComment
 * @package app\common\validate
 */
class ArticleComment extends BaseValidate
{
    protected $rule = [
        'parent_id'         => 'number|pidCommentExist',
        'article_id'        => 'number|articleExist',
        'content'           => 'require|min:1',
        'comment_id'        => 'require|number'
    ];

    protected $message = [
        'parent_id.int'             => '评论ID只能整数',
        'article_id.int'            => '文章ID只能整数',
        'content.require'           => '请输入评论内容',
    ];

    protected $scene = [
        'comment'    => [
            'article_id',
            'content',
        ],
        'reply'  => [
            'parent_id',
            'content',
        ],
        'more'   => [
            'article_id' => 'require|number|articleExist',
            'page'
        ],
        'moreCommentReply'  => [
            'comment_id'
        ]
    ];

    /**
     * @param $value
     * @return bool|string
     * @throws \think\exception\DbException
     */
    protected function pidCommentExist($value)
    {
        $isExist = ArticleCommentModel::get($value);
        if (!$isExist) {
            return '此评论不存在';
        }
        return true;
    }

    /**
     * @param $value
     * @return bool|string
     * @throws \think\exception\DbException
     */
    protected function articleExist($value)
    {
        $isExist = Article::get($value);
        if (!$isExist) {
            return '此文章不存在';
        }
        return true;
    }
}