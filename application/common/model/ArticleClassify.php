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

namespace app\common\model;

/**
 * 圈子 文章分类
 * Class ArticleClassify
 * @package app\common\model
 */
class ArticleClassify extends Model
{
    /**
     * 隐藏输出字段
     * @var array
     */
    protected $hidden = [
        'parent_id',
        'state'
    ];

    /**
     * 获取文章分类
     * @param int $parentId  父分类ID
     * @return false|static[]
     */
    static public function getClassify($parentId = 0)
    {
        return self::all(['parent_id' => $parentId, 'state' => 1]);
    }
}